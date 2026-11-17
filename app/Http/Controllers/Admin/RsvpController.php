<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rsvp;
use App\Models\Wedding;
use App\Support\MalayFormatter;
use App\Support\SearchTerm;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RsvpController extends Controller
{
    public function index(Request $request, Wedding $wedding): View
    {
        $stats = $wedding->rsvps()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END), 0) as hadir")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'tidak_hadir' THEN 1 ELSE 0 END), 0) as tidak_hadir")
            ->selectRaw('COALESCE(SUM(total_pax), 0) as pax')
            ->first();

        return view('admin.rsvps.index', [
            'wedding' => $wedding,
            'rsvps' => $this->filtered($request, $wedding)->paginate(15)->withQueryString(),
            'stats' => [
                'total' => (int) ($stats->total ?? 0),
                'hadir' => (int) ($stats->hadir ?? 0),
                'tidak_hadir' => (int) ($stats->tidak_hadir ?? 0),
                'pax' => (int) ($stats->pax ?? 0),
            ],
        ]);
    }

    public function export(Request $request, Wedding $wedding): StreamedResponse
    {
        $filename = 'rsvp-'.$wedding->slug.'.csv';

        return response()->streamDownload(function () use ($request, $wedding): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Nama Tetamu', 'Nombor Telefon', 'Status', 'Jumlah Pax', 'Tarikh RSVP']);

            foreach ($this->filtered($request, $wedding)->cursor() as $rsvp) {
                fputcsv($handle, [
                    $rsvp->guest_name,
                    $rsvp->phone_number,
                    $rsvp->statusLabel(),
                    $rsvp->total_pax,
                    $rsvp->created_at ? MalayFormatter::dateTime($rsvp->created_at) : '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function destroy(Wedding $wedding, Rsvp $rsvp): RedirectResponse
    {
        abort_unless($rsvp->wedding_id === $wedding->id, 404);

        $rsvp->delete();

        return redirect()
            ->route('admin.weddings.rsvps.index', $wedding)
            ->with('success', 'Rekod RSVP telah dipadam.');
    }

    private function filtered(Request $request, Wedding $wedding): HasMany
    {
        $query = $wedding->rsvps()->latest();
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where('guest_name', 'like', SearchTerm::like($search));
        }

        $status = (string) $request->query('status', '');

        if (in_array($status, [Rsvp::HADIR, Rsvp::TIDAK_HADIR], true)) {
            $query->where('status', $status);
        }

        return $query;
    }
}
