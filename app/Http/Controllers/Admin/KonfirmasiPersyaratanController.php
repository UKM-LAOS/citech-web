<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusRegistrasi;
use App\Http\Controllers\Controller;
use App\Models\DokumenRegistrasi;
use App\Models\Tim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class KonfirmasiPersyaratanController extends Controller
{
    /**
     * Display the registration documents confirmation page.
     */
    public function index(): Response
    {
        $teams = Tim::with(['members', 'dokumen_registrasi'])->get();

        return Inertia::render('admin/KonfirmasiPersyaratan', [
            'teams' => $teams,
        ]);
    }

    /**
     * Update status (approve/reject) of registration documents.
     */
    public function update(Request $request, int $id_registrasi): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:berhasil,ditolak',
            'catatan' => 'required_if:status,ditolak|nullable|string|max:1000',
        ], [
            'status.required' => 'Status konfirmasi harus ditentukan.',
            'status.in' => 'Status konfirmasi tidak valid.',
            'catatan.required_if' => 'Catatan penolakan wajib diisi jika status ditolak.',
            'catatan.max' => 'Catatan penolakan maksimal 1000 karakter.',
        ]);

        /** @var DokumenRegistrasi $dokumen */
        $dokumen = DokumenRegistrasi::findOrFail($id_registrasi);

        // Guard: prevent double-processing — only allow changes when status is still pending
        if ($dokumen->status_registrasi !== StatusRegistrasi::Pending->value) {
            throw ValidationException::withMessages([
                'status' => 'Dokumen ini sudah diproses sebelumnya dan tidak dapat diubah lagi.',
            ]);
        }

        $dokumen->update([
            'status_registrasi' => $request->status,
            'catatan_registrasi' => $request->status === StatusRegistrasi::Ditolak->value ? $request->catatan : null,
        ]);

        return redirect()->back()->with('success', 'Status dokumen berhasil diperbarui!');
    }
}
