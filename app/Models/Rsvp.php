<?php

namespace App\Models;

use Database\Factories\RsvpFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['guest_name', 'phone_number', 'status', 'total_pax'])]
class Rsvp extends Model
{
    /** @use HasFactory<RsvpFactory> */
    use HasFactory;

    public const HADIR = 'hadir';

    public const TIDAK_HADIR = 'tidak_hadir';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_pax' => 'integer',
        ];
    }

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }

    public function statusLabel(): string
    {
        return $this->status === self::HADIR ? 'Hadir' : 'Tidak Hadir';
    }
}
