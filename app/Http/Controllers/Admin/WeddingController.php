<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WeddingRequest;
use App\Models\Wedding;
use App\Services\WeddingSaver;
use App\Support\InvitationPage;
use App\Support\InvitationTheme;
use App\Support\SearchTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeddingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Wedding::query()->withCount(['rsvps', 'wishes'])->latest();
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $like = SearchTerm::like($search);
            $query->where(function ($builder) use ($like): void {
                $builder->where('groom_short_name', 'like', $like)
                    ->orWhere('bride_short_name', 'like', $like)
                    ->orWhere('slug', 'like', $like);
            });
        }

        if ($request->query('status') === 'aktif') {
            $query->where('is_active', true);
        } elseif ($request->query('status') === 'tidak_aktif') {
            $query->where('is_active', false);
        }

        return view('admin.weddings.index', [
            'weddings' => $query->paginate(10)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.weddings.form', [
            'wedding' => new Wedding([
                'is_active' => true,
                'time_start' => '11:00',
                'time_end' => '16:00',
                'decoration_settings' => InvitationTheme::resolve(null),
            ]),
        ]);
    }

    public function store(WeddingRequest $request, WeddingSaver $saver): RedirectResponse
    {
        $wedding = $saver->create(
            $request->validated(),
            $request->file('cover_image'),
            $request->file('music_file'),
        );

        return redirect()
            ->route('admin.weddings.edit', $wedding)
            ->with('success', 'Kad kahwin berjaya dicipta. Anda boleh pratonton sebelum menerbitkannya.');
    }

    public function edit(Wedding $wedding): View
    {
        return view('admin.weddings.form', [
            'wedding' => $wedding,
        ]);
    }

    public function update(WeddingRequest $request, Wedding $wedding, WeddingSaver $saver): RedirectResponse
    {
        $saver->update(
            $wedding,
            $request->validated(),
            $request->file('cover_image'),
            $request->file('music_file'),
        );

        return redirect()
            ->route('admin.weddings.edit', $wedding)
            ->with('success', 'Maklumat kad kahwin telah dikemaskini.');
    }

    public function destroy(Wedding $wedding): RedirectResponse
    {
        $wedding->delete();

        return redirect()
            ->route('admin.weddings.index')
            ->with('success', 'Kad kahwin telah dipadam bersama RSVP dan ucapan berkaitan.');
    }

    public function status(Request $request, Wedding $wedding): RedirectResponse
    {
        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $wedding->update([
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', $wedding->is_active
            ? 'Jemputan telah diaktifkan.'
            : 'Jemputan telah dinyahaktifkan.');
    }

    public function preview(Wedding $wedding): \Illuminate\Http\Response
    {
        return response()
            ->view('wedding', InvitationPage::data($wedding, true))
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'private, no-store');
    }
}
