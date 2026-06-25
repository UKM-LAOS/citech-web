<?php

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusRegistrasi;
use App\Enums\StatusSeleksi;
use App\Http\Controllers\Controller;
use App\Models\MemberTim;
use App\Models\Tim;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TimController extends Controller
{
    /**
     * Display team details.
     */
    public function index(): Response
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        $user = $authUser->load('tim.members', 'tim.dokumen_registrasi', 'tim.pembayaran', 'tim.submission');

        return Inertia::render('peserta/Tim', [
            'userTeam' => $user->tim,
            'teamMembers' => $user->tim ? $user->tim->members : [],
            'isSubmissionOpen' => Timeline::isOpenForTahap('pendaftaran_b2'),
        ]);
    }

    /**
     * Handle creation or updation of team data.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Tim|null $existingTim */
        $existingTim = $user->tim;

        if ($existingTim && $existingTim->dokumen_registrasi) {
            $statusReg = $existingTim->dokumen_registrasi->status_registrasi;
            if ($statusReg === StatusRegistrasi::Berhasil->value) {
                throw ValidationException::withMessages([
                    'nama_tim' => 'Data tim tidak dapat diubah karena berkas persyaratan pendaftaran sudah disetujui.',
                ]);
            }
            if ($statusReg === StatusRegistrasi::Pending->value) {
                throw ValidationException::withMessages([
                    'nama_tim' => 'Data tim tidak dapat diubah saat berkas persyaratan pendaftaran sedang diverifikasi oleh panitia.',
                ]);
            }
        }

        $request->validate([
            'nama_tim' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tim', 'nama_tim')->ignore($existingTim?->id_tim, 'id_tim'),
            ],
            'universitas' => 'required|string|max:255',
            // Ketua details
            'nim_ketua' => 'required|string|max:255',
            'jurusan_ketua' => 'required|string|max:255',
            // Anggota 1
            'nama_anggota1' => 'required|string|max:255',
            'nim_anggota1' => 'required|string|max:255',
            'jurusan_anggota1' => 'required|string|max:255',
            // Anggota 2
            'nama_anggota2' => 'nullable|string|max:255',
            'nim_anggota2' => 'nullable|string|max:255',
            'jurusan_anggota2' => 'nullable|string|max:255',
        ]);

        $universitas = $request->universitas;
        $memberIds = $existingTim ? $existingTim->members->pluck('id_member')->toArray() : [];

        // Check if NIM is unique within the same university (cross-team validation)
        $checkNimUnique = function ($nim, $universitas, $field, $label) use ($memberIds) {
            if (! $nim) {
                return;
            }
            $exists = MemberTim::where('nim_peserta', $nim)
                ->whereNotIn('id_member', $memberIds)
                ->whereHas('tim', function ($query) use ($universitas) {
                    $query->where('universitas', $universitas);
                })
                ->exists();
            if ($exists) {
                throw ValidationException::withMessages([
                    $field => 'NIM '.$nim.' sudah terdaftar untuk '.$label.' dari universitas ini di tim lain.',
                ]);
            }
        };

        $checkNimUnique($request->nim_ketua, $universitas, 'nim_ketua', 'ketua');
        $checkNimUnique($request->nim_anggota1, $universitas, 'nim_anggota1', 'anggota 1');
        $checkNimUnique($request->nim_anggota2, $universitas, 'nim_anggota2', 'anggota 2');

        // Check duplicate NIM entries within the same team
        if ($request->nim_ketua === $request->nim_anggota1) {
            throw ValidationException::withMessages([
                'nim_anggota1' => 'NIM anggota 1 tidak boleh sama dengan NIM ketua.',
            ]);
        }
        if ($request->nim_anggota2) {
            if ($request->nim_ketua === $request->nim_anggota2) {
                throw ValidationException::withMessages([
                    'nim_anggota2' => 'NIM anggota 2 tidak boleh sama dengan NIM ketua.',
                ]);
            }
            if ($request->nim_anggota1 === $request->nim_anggota2) {
                throw ValidationException::withMessages([
                    'nim_anggota2' => 'NIM anggota 2 tidak boleh sama dengan NIM anggota 1.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $user, $existingTim) {
            $tim = Tim::updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nama_tim' => $request->nama_tim,
                    'universitas' => $request->universitas,
                    'status_seleksi' => $existingTim ? $existingTim->status_seleksi : StatusSeleksi::BelumSeleksi->value,
                    'batch' => $existingTim ? $existingTim->batch : 1,
                ]
            );

            // Recreate members safely
            $tim->members()->delete();

            // Ketua (Leader)
            $tim->members()->create([
                'nama_peserta' => $user->name,
                'nim_peserta' => $request->nim_ketua,
                'jurusan' => $request->jurusan_ketua,
                'role' => 'ketua',
            ]);

            // Anggota 1 (Member 1)
            $tim->members()->create([
                'nama_peserta' => $request->nama_anggota1,
                'nim_peserta' => $request->nim_anggota1,
                'jurusan' => $request->jurusan_anggota1,
                'role' => 'anggota',
            ]);

            // Anggota 2 (Member 2 - optional)
            if ($request->nama_anggota2 && $request->nim_anggota2 && $request->jurusan_anggota2) {
                $tim->members()->create([
                    'nama_peserta' => $request->nama_anggota2,
                    'nim_peserta' => $request->nim_anggota2,
                    'jurusan' => $request->jurusan_anggota2,
                    'role' => 'anggota',
                ]);
            }
        });

        return redirect()->route('peserta.tim')->with('success', 'Data tim berhasil disimpan!');
    }
}
