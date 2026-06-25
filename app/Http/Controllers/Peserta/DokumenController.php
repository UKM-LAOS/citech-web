<?php

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusRegistrasi;
use App\Http\Controllers\Controller;
use App\Models\DokumenRegistrasi;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DokumenController extends Controller
{
    /**
     * Upload or update team registration documents.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Tim|null $tim */
        $tim = $user->tim;
        if (! $tim) {
            throw ValidationException::withMessages([
                'file_dokumen' => 'Anda harus membuat tim terlebih dahulu.',
            ]);
        }

        if ($tim->dokumen_registrasi && $tim->dokumen_registrasi->status_registrasi === StatusRegistrasi::Berhasil->value) {
            throw ValidationException::withMessages([
                'file_dokumen' => 'Berkas persyaratan pendaftaran sudah disetujui dan tidak dapat diubah.',
            ]);
        }

        $request->validate([
            'file_dokumen' => 'required|file|mimes:pdf|max:5120',
        ], [
            'file_dokumen.required' => 'Berkas persyaratan wajib diunggah.',
            'file_dokumen.file' => 'Berkas yang diunggah harus berupa file.',
            'file_dokumen.mimes' => 'Berkas harus dalam format PDF.',
            'file_dokumen.max' => 'Ukuran berkas maksimal adalah 5MB.',
        ]);

        if ($tim->dokumen_registrasi) {
            Storage::disk('public')->delete($tim->dokumen_registrasi->link_file_registrasi);
        }

        $path = $request->file('file_dokumen')->store('dokumen_registrasi', 'public');

        DokumenRegistrasi::updateOrCreate(
            ['id_tim' => $tim->id_tim],
            [
                'link_file_registrasi' => $path,
                'status_registrasi' => StatusRegistrasi::Pending->value,
                'catatan_registrasi' => null,
                'uploaded_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Berkas persyaratan berhasil diunggah!');
    }

    /**
     * Cancel/delete the uploaded registration document.
     */
    public function destroy(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Tim|null $tim */
        $tim = $user->tim;
        if (! $tim || ! $tim->dokumen_registrasi) {
            throw ValidationException::withMessages([
                'file_dokumen' => 'Dokumen tidak ditemukan.',
            ]);
        }

        if ($tim->dokumen_registrasi->status_registrasi === StatusRegistrasi::Berhasil->value) {
            throw ValidationException::withMessages([
                'file_dokumen' => 'Berkas persyaratan pendaftaran sudah disetujui dan tidak dapat dibatalkan.',
            ]);
        }

        Storage::disk('public')->delete($tim->dokumen_registrasi->link_file_registrasi);
        $tim->dokumen_registrasi->delete();

        return redirect()->back()->with('success', 'Unggahan berkas berhasil dibatalkan.');
    }
}
