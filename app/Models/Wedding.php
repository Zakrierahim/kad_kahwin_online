<?php

namespace App\Models;

use App\Support\InvitationTheme;
use App\Support\MalayFormatter;
use App\Support\MalaysianPhone;
use App\Support\SafeUrl;
use App\Support\YouTube;
use Database\Factories\WeddingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'slug',
    'groom_short_name',
    'bride_short_name',
    'groom_full_name',
    'bride_full_name',
    'parents_names',
    'hashtag',
    'date',
    'time_start',
    'time_end',
    'location_name',
    'location_address',
    'maps_url',
    'waze_url',
    'family_contacts',
    'bg_music_url',
    'bg_music_path',
    'cover_image_path',
    'decoration_settings',
    'is_active',
])]
class Wedding extends Model
{
    /** @use HasFactory<WeddingFactory> */
    use HasFactory;

    public const RESERVED_SLUGS = [
        'admin',
        'api',
        'build',
        'login',
        'logout',
        'sanctum',
        'storage',
        'up',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Wedding $wedding): void {
            $wedding->deleteStoredFile($wedding->cover_image_path);
            $wedding->deleteStoredFile($wedding->bg_music_path);
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'family_contacts' => 'array',
            'decoration_settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    public function wishes(): HasMany
    {
        return $this->hasMany(Wish::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('date', '>=', now()->toDateString())->orderBy('date');
    }

    public function coupleLabel(): string
    {
        return $this->groom_short_name.' & '.$this->bride_short_name;
    }

    public function initials(): string
    {
        return mb_strtoupper(mb_substr($this->groom_short_name, 0, 1).mb_substr($this->bride_short_name, 0, 1));
    }

    public function statusLabel(): string
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    public function publicUrl(): string
    {
        return route('invitation.show', $this->slug);
    }

    public function whatsappShareUrl(): string
    {
        $text = "Assalamualaikum, kami menjemput anda ke majlis perkahwinan {$this->groom_short_name} & {$this->bride_short_name}.\n".$this->publicUrl();

        return 'https://wa.me/?text='.rawurlencode($text);
    }

    public function shareDescription(): string
    {
        return sprintf(
            'Anda dijemput ke majlis perkahwinan %s dan %s pada %s di %s.',
            $this->groom_full_name,
            $this->bride_full_name,
            $this->malayDate(),
            $this->location_name,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function resolvedTheme(): array
    {
        return InvitationTheme::resolve($this->decoration_settings);
    }

    public function malayDate(): string
    {
        return MalayFormatter::date($this->date);
    }

    public function malayTimeStart(): string
    {
        return MalayFormatter::time((string) $this->time_start);
    }

    public function malayTimeEnd(): string
    {
        return MalayFormatter::time((string) $this->time_end);
    }

    public function timeForInput(string $field): ?string
    {
        $value = $this->getAttribute($field);

        if (! is_string($value) || $value === '') {
            return null;
        }

        return substr($value, 0, 5);
    }

    public function startsAt(): Carbon
    {
        $time = substr((string) $this->time_start, 0, 8);

        if (strlen($time) === 5) {
            $time .= ':00';
        }

        return Carbon::parse($this->date->format('Y-m-d').' '.$time, 'Asia/Kuala_Lumpur');
    }

    public function countdownTarget(): string
    {
        return $this->startsAt()->toIso8601String();
    }

    public function hasStarted(): bool
    {
        return now('Asia/Kuala_Lumpur')->greaterThanOrEqualTo($this->startsAt());
    }

    public function safeMapsUrl(): ?string
    {
        return SafeUrl::https($this->maps_url);
    }

    public function safeWazeUrl(): ?string
    {
        return SafeUrl::https($this->waze_url);
    }

    public function coverImageUrl(): ?string
    {
        return $this->publicFileUrl($this->cover_image_path);
    }

    public function musicUrl(): ?string
    {
        return $this->publicFileUrl($this->bg_music_path) ?? SafeUrl::https($this->bg_music_url);
    }

    public function youtubeVideoId(): ?string
    {
        if ($this->publicFileUrl($this->bg_music_path) !== null) {
            return null;
        }

        return YouTube::videoId($this->bg_music_url);
    }

    /**
     * @return list<array{name: string, phone: string, url: ?string}>
     */
    public function whatsappContacts(): array
    {
        $contacts = [];

        foreach ($this->family_contacts ?? [] as $contact) {
            if (! is_array($contact)) {
                continue;
            }

            $name = trim((string) ($contact['name'] ?? ''));
            $display = trim((string) ($contact['phone'] ?? ''));

            if ($name === '' || $display === '') {
                continue;
            }

            $number = MalaysianPhone::toWaMe($display);
            $text = 'Assalamualaikum, saya ingin bertanya mengenai majlis perkahwinan '.$this->coupleLabel().'.';

            $contacts[] = [
                'name' => $name,
                'phone' => $display,
                'url' => $number ? 'https://wa.me/'.$number.'?text='.rawurlencode($text) : null,
            ];
        }

        return $contacts;
    }

    public function deleteStoredFile(?string $path): void
    {
        if (! is_string($path) || $path === '' || str_contains($path, '..') || str_starts_with($path, '/')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private function publicFileUrl(?string $path): ?string
    {
        if (! is_string($path) || $path === '' || str_contains($path, '..') || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
