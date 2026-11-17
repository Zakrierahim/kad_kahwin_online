<?php

namespace App\Support;

final class SearchTerm
{
    public static function like(string $value): string
    {
        return '%'.addcslashes(trim($value), '%_\\').'%';
    }
}
