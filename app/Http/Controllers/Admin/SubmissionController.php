<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tim;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
{
    /**
     * Display list of submissions.
     */
    public function index(): Response
    {
        $teams = Tim::with(['members', 'submission'])
            ->whereHas('submission')
            ->get();

        return Inertia::render('admin/Submission', [
            'teams' => $teams,
        ]);
    }
}
