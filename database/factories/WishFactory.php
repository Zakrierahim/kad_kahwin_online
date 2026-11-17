<?php

namespace Database\Factories;

use App\Models\Wedding;
use App\Models\Wish;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wish>
 */
class WishFactory extends Factory
{
    protected $model = Wish::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wedding_id' => Wedding::factory(),
            'guest_name' => fake()->name(),
            'message' => 'Semoga berbahagia hingga ke jannah.',
        ];
    }
}
