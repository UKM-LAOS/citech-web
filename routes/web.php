<?php

use App\Models\Timeline;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $activeTimeline = Timeline::where('tanggal_selesai', '>', now())
        ->orderBy('tanggal_selesai', 'asc')
        ->first() ?? Timeline::orderBy('tanggal_selesai', 'desc')->first();

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'activeTimeline' => $activeTimeline,
        'allTimelines' => Timeline::orderBy('tanggal_mulai', 'asc')->get(),
    ]);
});

Route::get('/dashboard', function () {
    if (auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    $user = auth()->user()->load('tim.members');
    $activeTimeline = Timeline::where('tanggal_selesai', '>', now())
        ->orderBy('tanggal_selesai', 'asc')
        ->first() ?? Timeline::orderBy('tanggal_selesai', 'desc')->first();

    return Inertia::render('Dashboard', [
        'activeTimeline' => $activeTimeline,
        'allTimelines' => Timeline::orderBy('tanggal_mulai', 'asc')->get(),
        'userTeam' => $user->tim,
        'teamMembers' => $user->tim ? $user->tim->members : [],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Peserta Routes
Route::middleware(['auth', 'verified', 'peserta'])->group(function () {
    Route::get('/dashboard/tim', function () {
        $user = auth()->user()->load('tim.members');
        return Inertia::render('peserta/Tim', [
            'userTeam' => $user->tim,
            'teamMembers' => $user->tim ? $user->tim->members : [],
        ]);
    })->name('peserta.tim');

    Route::get('/dashboard/profil', function () {
        return Inertia::render('peserta/Profil');
    })->name('peserta.profil');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('admin/Dashboard');
    })->name('admin.dashboard');

    Route::get('/konfirmasi-persyaratan', function () {
        return Inertia::render('admin/KonfirmasiPersyaratan');
    })->name('admin.persyaratan');

    Route::get('/konfirmasi-pembayaran', function () {
        return Inertia::render('admin/KonfirmasiPembayaran');
    })->name('admin.pembayaran');

    Route::get('/tim-terdaftar', function () {
        return Inertia::render('admin/TimTerdaftar');
    })->name('admin.tim-terdaftar');

    Route::get('/submission', function () {
        return Inertia::render('admin/Submission');
    })->name('admin.submission');

    Route::get('/atur-tanggal', function () {
        return Inertia::render('admin/AturTanggal');
    })->name('admin.atur-tanggal');

    Route::get('/kelola-sponsor', function () {
        return Inertia::render('admin/KelolaSponsor');
    })->name('admin.kelola-sponsor');
});

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';

