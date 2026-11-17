<?php

namespace Database\Factories;

use App\Models\Wedding;
use App\Support\InvitationTheme;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Wedding>
 */
class WeddingFactory extends Factory
{
    protected $model = Wedding::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $groom = fake()->firstNameMale();
        $bride = fake()->firstNameFemale();

        return [
            'slug' => Str::slug($groom.'-dan-'.$bride.'-'.fake()->unique()->numerify('###')),
            'groom_short_name' => $groom,
            'bride_short_name' => $bride,
            'groom_full_name' => $groom.' bin Ahmad',
            'bride_full_name' => $bride.' binti Hassan',
            'parents_names' => 'Ahmad bin Abdullah & Hassan bin Osman',
            'date' => now()->addMonths(2)->toDateString(),
            'time_start' => '11:00:00',
            'time_end' => '16:00:00',
            'location_name' => 'Dewan Seri Melati',
            'location_address' => "Jalan Ampang\n50450 Kuala Lumpur",
            'maps_url' => 'https://maps.google.com/?q=Dewan+Seri+Melati',
            'waze_url' => 'https://www.waze.com/ul?q=Dewan+Seri+Melati&navigate=yes',
            'family_contacts' => [
                ['name' => 'Ahmad', 'phone' => '0123456789'],
            ],
            'bg_music_url' => null,
            'bg_music_path' => null,
            'cover_image_path' => null,
            'decoration_settings' => InvitationTheme::resolve(['theme' => 'elegant_cream']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
