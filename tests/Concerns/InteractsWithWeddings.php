<?php

namespace Tests\Concerns;

use App\Models\User;

trait InteractsWithWeddings
{
    protected function actingAdmin(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    protected function weddingPayload(array $overrides = []): array
    {
        return array_merge([
            'groom_short_name' => 'Ali',
            'bride_short_name' => 'Fatimah',
            'groom_full_name' => 'Ali bin Ahmad',
            'bride_full_name' => 'Fatimah binti Hassan',
            'parents_names' => "Ahmad bin Abdullah\nAminah binti Abu",
            'date' => '2026-11-21',
            'time_start' => '11:00',
            'time_end' => '16:00',
            'location_name' => 'Dewan Seri Melati',
            'location_address' => "Jalan Ampang\n50450 Kuala Lumpur",
            'maps_url' => 'https://maps.google.com/?q=Dewan+Seri+Melati',
            'waze_url' => 'https://www.waze.com/ul?q=Dewan+Seri+Melati&navigate=yes',
            'contacts' => [
                ['name' => 'Ahmad', 'phone' => '0123456789'],
            ],
            'theme' => 'elegant_cream',
            'primary_color' => '#B08D57',
            'secondary_color' => '#EADCC8',
            'background_color' => '#FFF9F0',
            'font' => 'serif',
            'floral_style' => 'classic',
            'music_enabled' => '0',
            'bg_music_url' => '',
            'is_active' => '1',
            'slug' => 'ali-dan-fatimah',
        ], $overrides);
    }
}
