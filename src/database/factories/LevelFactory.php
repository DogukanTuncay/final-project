<?php

namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Level>
 */
class LevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Level::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $levelNumber = $this->faker->unique()->numberBetween(1, 10); // Ensure unique level numbers for testing
        $minXp = ($levelNumber - 1) * 100;
        $maxXp = $levelNumber * 100;
        $enTitle = 'Level ' . $levelNumber;
        $trTitle = 'Seviye ' . $levelNumber;

        return [
            'level_number' => $levelNumber,
            'title' => [
                'en' => $enTitle,
                'tr' => $trTitle,
            ],
            'description' => [
                'en' => $this->faker->sentence,
                'tr' => $this->faker->sentence,
            ],
            'min_xp' => $minXp,
            'max_xp' => $maxXp,
            'icon' => null, // You can set a default icon path if needed
            'color_code' => $this->faker->hexColor,
            'is_active' => true,
            'required_exp' => $minXp, // Genellikle min_xp ile aynı olabilir veya farklı bir mantık olabilir
            'order' => $levelNumber, // order ve level_number genellikle aynı olabilir
        ];
    }
} 