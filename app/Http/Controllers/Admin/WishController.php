<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wedding;
use App\Models\Wish;
use App\Support\SearchTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishController extends Controller
{
    public function index(Request $request, Wedding $wedding): View
    {
        $query = $wedding->wishes()->latest();
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where('guest_name', 'like', SearchTerm::like($search));
        }

        return view('admin.wishes.index', [
            'wedding' => $wedding,
            'wishes' => $query->paginate(15)->withQueryString(),
        ]);
    }

    public function destroy(Wedding $wedding, Wish $wish): RedirectResponse
    {
        abort_unless($wish->wedding_id === $wedding->id, 404);

        $wish->delete();

        return redirect()
            ->route('admin.weddings.wishes.index', $wedding)
            ->with('success', 'Ucapan telah dipadam.');
    }
}
