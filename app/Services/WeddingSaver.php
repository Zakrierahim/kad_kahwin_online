<?php

namespace App\Services;

use App\Models\Wedding;
use App\Support\InvitationTheme;
use Illuminate\Http\UploadedFile;

class WeddingSaver
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function create(array $input, ?UploadedFile $cover, ?UploadedFile $music): Wedding
    {
        return Wedding::query()->create($this->payload($input, $cover, $music));
    }

    /**
     * @param  array<string, mixed>  $input
     */
    public function update(Wedding $wedding, array $input, ?UploadedFile $cover, ?UploadedFile $music): Wedding
    {
        $wedding->update($this->payload($input, $cover, $music, $wedding));

        return $wedding->refresh();
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function payload(array $input, ?UploadedFile $cover, ?UploadedFile $music, ?Wedding $existing = null): array
    {
        $themeKey = is_string($input['theme'] ?? null) && isset(InvitationTheme::THEMES[$input['theme']])
            ? $input['theme']
            : 'elegant_cream';
        $theme = InvitationTheme::THEMES[$themeKey];

        $contacts = collect($input['contacts'] ?? [])
            ->filter(fn ($contact) => is_array($contact))
            ->map(fn (array $contact) => [
                'name' => $this->plain($contact['name'] ?? ''),
                'phone' => trim(strip_tags((string) ($contact['phone'] ?? ''))),
            ])
            ->filter(fn (array $contact) => $contact['name'] !== '' && $contact['phone'] !== '')
            ->values()
            ->all();

        $payload = [
            'slug' => $input['slug'],
            'groom_short_name' => $this->plain($input['groom_short_name'] ?? ''),
            'bride_short_name' => $this->plain($input['bride_short_name'] ?? ''),
            'groom_full_name' => $this->plain($input['groom_full_name'] ?? ''),
            'bride_full_name' => $this->plain($input['bride_full_name'] ?? ''),
            'parents_names' => $this->nullableText($input['parents_names'] ?? null),
            'hashtag' => $input['hashtag'] ?? null,
            'date' => $input['date'],
            'time_start' => $input['time_start'],
            'time_end' => $input['time_end'],
            'location_name' => $this->plain($input['location_name'] ?? ''),
            'location_address' => trim(strip_tags((string) ($input['location_address'] ?? ''))),
            'maps_url' => $input['maps_url'] ?: null,
            'waze_url' => $input['waze_url'] ?: null,
            'family_contacts' => $contacts,
            'bg_music_url' => $input['bg_music_url'] ?: null,
            'decoration_settings' => [
                'theme' => $themeKey,
                'primary_color' => strtoupper((string) ($input['primary_color'] ?: $theme['primary_color'])),
                'secondary_color' => strtoupper((string) ($input['secondary_color'] ?: $theme['secondary_color'])),
                'background_color' => strtoupper((string) ($input['background_color'] ?: $theme['background_color'])),
                'font' => $input['font'] ?? $theme['font'],
                'floral_style' => $input['floral_style'] ?? $theme['floral_style'],
                'music_enabled' => (bool) ($input['music_enabled'] ?? false),
            ],
            'is_active' => (bool) ($input['is_active'] ?? false),
        ];

        if ($cover instanceof UploadedFile) {
            $existing?->deleteStoredFile($existing->cover_image_path);
            $payload['cover_image_path'] = $cover->store('weddings/covers', 'public');
        } elseif ($existing && ! empty($input['remove_cover'])) {
            $existing->deleteStoredFile($existing->cover_image_path);
            $payload['cover_image_path'] = null;
        }

        if ($music instanceof UploadedFile) {
            $existing?->deleteStoredFile($existing->bg_music_path);
            $payload['bg_music_path'] = $music->store('weddings/music', 'public');
        } elseif ($existing && ! empty($input['remove_music'])) {
            $existing->deleteStoredFile($existing->bg_music_path);
            $payload['bg_music_path'] = null;
            $payload['bg_music_url'] = null;
        }

        return $payload;
    }

    private function plain(mixed $value): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags((string) $value)) ?? '');
    }

    private function nullableText(mixed $value): ?string
    {
        $clean = trim(strip_tags((string) $value));

        return $clean === '' ? null : $clean;
    }
}
