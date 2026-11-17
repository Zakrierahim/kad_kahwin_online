<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rsvp;
use App\Models\Wedding;
use App\Models\Wish;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $weddings = Wedding::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) as active')
            ->first();

        $rsvp = Rsvp::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END), 0) as hadir")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'tidak_hadir' THEN 1 ELSE 0 END), 0) as tidak_hadir")
            ->selectRaw('COALESCE(SUM(total_pax), 0) as pax')
            ->first();

        $total = (int) ($weddings->total ?? 0);
        $active = (int) ($weddings->active ?? 0);

        return view('admin.dashboard', [
            'stats' => [
                'weddings' => $total,
                'active' => $active,
                'inactive' => $total - $active,
                'rsvps' => (int) ($rsvp->total ?? 0),
                'hadir' => (int) ($rsvp->hadir ?? 0),
                'tidak_hadir' => (int) ($rsvp->tidak_hadir ?? 0),
                'pax' => (int) ($rsvp->pax ?? 0),
                'wishes' => Wish::query()->count(),
            ],
            'recent' => Wedding::query()->latest()->limit(5)->get(),
            'upcoming' => Wedding::query()->upcoming()->limit(5)->get(),
        ]);
    }
}
