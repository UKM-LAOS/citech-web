<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ProfilController extends Controller
{
    /**
     * Display participant profile.
     */
    public function index(): Response
    {
        return Inertia::render('peserta/Profil');
    }
}
