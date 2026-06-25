<?php

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusPembayaran;
use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tim;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    /**
     * Upload payment proof.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Tim|null $tim */
        $tim = $user->tim;
        if (! $tim) {
            throw ValidationException::withMessages([
                'bukti_pembayaran' => 'Anda harus membuat tim terlebih dahulu.',
            ]);
        }

        if ($tim->pembayaran && $tim->pembayaran->status_pembayaran === StatusPembayaran::Berhasil->value) {
            throw ValidationException::withMessages([
                'bukti_pembayaran' => 'Bukti pembayaran sudah disetujui dan tidak dapat diubah.',
            ]);
        }

        $request->validate([
            'bukti_pembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ], [
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.file' => 'Berkas yang diunggah harus berupa file.',
            'bukti_pembayaran.mimes' => 'Berkas harus dalam format JPG, PNG, atau PDF.',
            'bukti_pembayaran.max' => 'Ukuran berkas maksimal adalah 5MB.',
        ]);

        if ($tim->pembayaran) {
            Storage::disk('public')->delete($tim->pembayaran->bukti_pembayaran);
        }

        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        Pembayaran::updateOrCreate(
            ['id_tim' => $tim->id_tim],
            [
                'bukti_pembayaran' => $path,
                'status_pembayaran' => StatusPembayaran::Pending->value,
                'catatan_pembayaran' => null,
                'uploaded_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah!');
    }

    /**
     * Cancel/delete the uploaded payment proof.
     */
    public function destroy(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var Tim|null $tim */
        $tim = $user->tim;
        if (! $tim || ! $tim->pembayaran) {
            throw ValidationException::withMessages([
                'bukti_pembayaran' => 'Data pembayaran tidak ditemukan.',
            ]);
        }

        // Guard: prevent cancellation if payment already approved
        if ($tim->pembayaran->status_pembayaran === StatusPembayaran::Berhasil->value) {
            throw ValidationException::withMessages([
                'bukti_pembayaran' => 'Bukti pembayaran sudah disetujui dan tidak dapat dibatalkan.',
            ]);
        }

        Storage::disk('public')->delete($tim->pembayaran->bukti_pembayaran);
        $tim->pembayaran->delete();

        return redirect()->back()->with('success', 'Unggahan bukti pembayaran berhasil dibatalkan.');
    }
}
