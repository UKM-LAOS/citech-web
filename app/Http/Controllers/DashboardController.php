<?php

namespace App\Http\Controllers;

use App\Models\Timeline;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the user dashboard route.
     */
    public function __invoke(): Response|RedirectResponse
    {
        /** @var User $authUser */
        $authUser = auth()->user();
        if ($authUser->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $user = $authUser->load('tim.members', 'tim.dokumen_registrasi', 'tim.pembayaran', 'tim.submission');

        return Inertia::render('Dashboard', [
            'activeTimeline' => Timeline::currentActive(),
            'allTimelines' => Timeline::orderBy('tanggal_mulai', 'asc')->get(),
            'userTeam' => $user->tim,
            'teamMembers' => $user->tim ? $user->tim->members : [],
        ]);
    }
}
