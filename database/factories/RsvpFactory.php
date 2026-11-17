<?php

namespace Database\Factories;

use App\Models\Rsvp;
use App\Models\Wedding;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rsvp>
 */
class RsvpFactory extends Factory
{
    protected $model = Rsvp::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $attending = fake()->boolean();

        return [
            'wedding_id' => Wedding::factory(),
            'guest_name' => fake()->name(),
            'phone_number' => '012'.fake()->numerify('#######'),
            'status' => $attending ? Rsvp::HADIR : Rsvp::TIDAK_HADIR,
            'total_pax' => $attending ? fake()->numberBetween(1, 5) : 0,
        ];
    }
}
