<?php

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusSeleksi;
use App\Http\Controllers\Controller;
use App\Models\DokumenSubmission;
use App\Models\Tim;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubmissionController extends Controller
{
    /**
     * Submit team's proposal link.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Tim|null $tim */
        $tim = $user->tim;
        if (! $tim) {
            throw ValidationException::withMessages([
                'link_file_submission' => 'Anda harus membuat tim terlebih dahulu.',
            ]);
        }

        if ($tim->submission) {
            throw ValidationException::withMessages([
                'link_file_submission' => 'Anda sudah pernah mengumpulkan proposal.',
            ]);
        }

        if ($tim->status_seleksi !== StatusSeleksi::Penyisihan->value) {
            throw ValidationException::withMessages([
                'link_file_submission' => 'Anda hanya dapat mengumpulkan proposal setelah verifikasi berkas dan pembayaran disetujui oleh panitia.',
            ]);
        }

        if (! Timeline::isOpenForTahap('pendaftaran_b2')) {
            throw ValidationException::withMessages([
                'link_file_submission' => 'Batas waktu pengumpulan proposal (Batch 2) telah berakhir.',
            ]);
        }

        $request->validate([
            'link_file_submission' => 'required|url|max:1000',
        ], [
            'link_file_submission.required' => 'Link Google Drive wajib diisi.',
            'link_file_submission.url' => 'Format link harus berupa URL valid.',
            'link_file_submission.max' => 'Link maksimal 1000 karakter.',
        ]);

        DokumenSubmission::create([
            'id_tim' => $tim->id_tim,
            'link_file_submission' => $request->link_file_submission,
            'uploaded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Proposal berhasil dikumpulkan!');
    }
}
