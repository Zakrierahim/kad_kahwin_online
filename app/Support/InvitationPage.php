<?php

namespace App\Support;

use App\Models\Wedding;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class InvitationPage
{
    /**
     * @return array{wedding: Wedding, preview: bool, theme: array<string, mixed>, wishes: LengthAwarePaginator}
     */
    public static function data(Wedding $wedding, bool $preview = false): array
    {
        return [
            'wedding' => $wedding,
            'preview' => $preview,
            'theme' => $wedding->resolvedTheme(),
            'wishes' => $wedding->wishes()->latest()->paginate(8, ['*'], 'ucapan')->withQueryString(),
        ];
    }
}
