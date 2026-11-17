<?php

namespace App\Models;

use Database\Factories\WishFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['guest_name', 'message'])]
class Wish extends Model
{
    /** @use HasFactory<WishFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    public function wedding(): BelongsTo
    {
        return $this->belongsTo(Wedding::class);
    }
}
