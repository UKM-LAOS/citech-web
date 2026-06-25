<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusSeleksi;
use App\Http\Controllers\Controller;
use App\Models\Tim;
use Inertia\Inertia;
use Inertia\Response;

class TimTerdaftarController extends Controller
{
    /**
     * Display list of qualified teams.
     */
    public function index(): Response
    {
        $teams = Tim::with(['members', 'dokumen_registrasi', 'pembayaran'])
            ->whereIn('status_seleksi', StatusSeleksi::qualified())
            ->get();

        return Inertia::render('admin/TimTerdaftar', [
            'teams' => $teams,
        ]);
    }
}
