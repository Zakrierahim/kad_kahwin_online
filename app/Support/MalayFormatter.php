<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class MalayFormatter
{
    /**
     * @var array<int, string>
     */
    public const DAYS = [
        0 => 'Ahad',
        1 => 'Isnin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Khamis',
        5 => 'Jumaat',
        6 => 'Sabtu',
    ];

    /**
     * @var array<int, string>
     */
    public const MONTHS = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Mac',
        4 => 'April',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Julai',
        8 => 'Ogos',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Disember',
    ];

    public static function date(CarbonInterface $date): string
    {
        return sprintf(
            '%s, %d %s %d',
            self::DAYS[$date->dayOfWeek],
            $date->day,
            self::MONTHS[$date->month],
            $date->year,
        );
    }

    public static function dateTime(CarbonInterface $date): string
    {
        $local = $date->timezone('Asia/Kuala_Lumpur');

        return sprintf(
            '%d %s %d, %s',
            $local->day,
            self::MONTHS[$local->month],
            $local->year,
            $local->format('H:i'),
        );
    }

    public static function time(string $time): string
    {
        $parsed = Carbon::parse($time, 'Asia/Kuala_Lumpur');
        $hour = (int) $parsed->format('G');
        $period = match (true) {
            $hour < 12 => 'pagi',
            $hour < 15 => 'tengah hari',
            $hour < 19 => 'petang',
            default => 'malam',
        };

        return $parsed->format('g:i').' '.$period;
    }
}
