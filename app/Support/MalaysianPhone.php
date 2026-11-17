<?php

namespace App\Support;

final class MalaysianPhone
{
    public static function toWaMe(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '600')) {
            $digits = '60'.substr($digits, 3);
        }

        if (str_starts_with($digits, '60')) {
            // Already in international form.
        } elseif (str_starts_with($digits, '0')) {
            $digits = '60'.substr($digits, 1);
        } else {
            $digits = '60'.$digits;
        }

        if (preg_match('/^60\d{8,11}$/', $digits) !== 1) {
            return null;
        }

        return $digits;
    }
}
