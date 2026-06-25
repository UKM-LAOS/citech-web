<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\Timeline;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the welcome/landing page.
     */
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'activeTimeline' => Timeline::currentActive(),
            'allTimelines' => Timeline::orderBy('tanggal_mulai', 'asc')->get(),
            'sponsors' => Sponsor::where('is_active', true)
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }
}
