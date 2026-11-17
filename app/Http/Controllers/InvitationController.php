<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRsvpRequest;
use App\Http\Requests\StoreWishRequest;
use App\Models\Rsvp;
use App\Models\Wedding;
use App\Support\InvitationPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(string $slug): View|Response
    {
        $wedding = Wedding::query()->where('slug', $slug)->firstOrFail();

        if (! $wedding->is_active) {
            return response()
                ->view('invitation.unavailable', [], 404)
                ->header('Cache-Control', 'no-store');
        }

        return view('wedding', InvitationPage::data($wedding));
    }

    public function rsvp(StoreRsvpRequest $request, string $slug): RedirectResponse
    {
        $wedding = Wedding::query()->active()->where('slug', $slug)->firstOrFail();
        $status = $request->validated('status');

        $wedding->rsvps()->create([
            'guest_name' => $request->validated('rsvp_name'),
            'phone_number' => $request->validated('phone_number'),
            'status' => $status,
            'total_pax' => $status === Rsvp::TIDAK_HADIR ? 0 : (int) $request->validated('total_pax'),
        ]);

        return redirect()
            ->to($wedding->publicUrl().'#sahkan-kehadiran')
            ->with('rsvp_success', 'Terima kasih. Kehadiran anda telah direkodkan.');
    }

    public function wish(StoreWishRequest $request, string $slug): RedirectResponse
    {
        $wedding = Wedding::query()->active()->where('slug', $slug)->firstOrFail();

        $wedding->wishes()->create([
            'guest_name' => $request->validated('wish_name'),
            'message' => $request->validated('message'),
        ]);

        return redirect()
            ->to($wedding->publicUrl().'#buku-ucapan')
            ->with('wish_success', 'Terima kasih. Ucapan anda telah disimpan.');
    }
}
