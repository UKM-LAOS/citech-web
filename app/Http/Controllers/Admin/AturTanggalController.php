<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timeline;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AturTanggalController extends Controller
{
    /**
     * Display the timeline setting page.
     */
    public function index(): Response
    {
        $timelines = Timeline::orderByRaw(
            "FIELD(tahap, 'pendaftaran_b1', 'pendaftaran_b2', 'penyisihan', 'final', 'awarding')"
        )->get();

        return Inertia::render('admin/AturTanggal', [
            'timelines' => $timelines,
        ]);
    }

    /**
     * Update the timelines config.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $tahaps = ['pendaftaran_b1', 'pendaftaran_b2', 'penyisihan', 'final', 'awarding'];

        $rules = [];
        foreach ($tahaps as $tahap) {
            $rules["tanggal_mulai_{$tahap}"] = 'required|date';
            $rules["tanggal_selesai_{$tahap}"] = "required|date|after:tanggal_mulai_{$tahap}";
        }

        $messages = [];
        foreach ($tahaps as $tahap) {
            $messages["tanggal_selesai_{$tahap}.after"] = "Tanggal selesai harus setelah tanggal mulai untuk tahap {$tahap}.";
        }

        $request->validate($rules, $messages);

        DB::transaction(function () use ($request, $user, $tahaps) {
            foreach ($tahaps as $tahap) {
                Timeline::updateOrCreate(
                    ['tahap' => $tahap],
                    [
                        'tanggal_mulai' => $request->input("tanggal_mulai_{$tahap}"),
                        'tanggal_selesai' => $request->input("tanggal_selesai_{$tahap}"),
                        'updated_by' => $user->id_user,
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Timeline berhasil diperbarui!');
    }
}
