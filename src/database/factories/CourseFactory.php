<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = [
            'tr' => $this->faker->sentence(3),
            'en' => $this->faker->sentence(3)
        ];
        
        $slug = Str::slug($name['en']);
        
        $shortDescription = [
            'tr' => $this->faker->paragraph(1),
            'en' => $this->faker->paragraph(1)
        ];
        
        $description = [
            'tr' => $this->faker->paragraphs(3, true),
            'en' => $this->faker->paragraphs(3, true)
        ];
        
        $objectives = [
            'tr' => [
                $this->faker->sentence(),
                $this->faker->sentence(),
                $this->faker->sentence()
            ],
            'en' => [
                $this->faker->sentence(),
                $this->faker->sentence(),
                $this->faker->sentence()
            ]
        ];
        
        $metaTitle = [
            'tr' => $this->faker->sentence(5),
            'en' => $this->faker->sentence(5)
        ];
        
        $metaDescription = [
            'tr' => $this->faker->paragraph(1),
            'en' => $this->faker->paragraph(1)
        ];
        
        return [
            'slug' => $slug,
            'image' => 'images/course/default.jpg',
            'images' => json_encode([
                'images/course/default1.jpg',
                'images/course/default2.jpg'
            ]),
            'is_active' => $this->faker->boolean(80),
            'order' => $this->faker->numberBetween(1, 100),
            'category' => $this->faker->randomElement(array_keys(Course::CATEGORIES)),
            'difficulty' => $this->faker->randomElement(array_keys(Course::DIFFICULTIES)),
            'is_featured' => $this->faker->boolean(20),
            'name' => $name,
            'short_description' => $shortDescription,
            'description' => $description,
            'objectives' => $objectives,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription
        ];
    }
    
    /**
     * Belirli bir kategoride kurs oluştur
     *
     * @param string $category
     * @return static
     */
    public function category(string $category)
    {
        return $this->state(function (array $attributes) use ($category) {
            return [
                'category' => $category,
            ];
        });
    }
    
    /**
     * Belirli bir zorluk seviyesinde kurs oluştur
     *
     * @param string $difficulty
     * @return static
     */
    public function difficulty(string $difficulty)
    {
        return $this->state(function (array $attributes) use ($difficulty) {
            return [
                'difficulty' => $difficulty,
            ];
        });
    }
    
    /**
     * Aktif kurs oluştur
     *
     * @return static
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }
    
    /**
     * Öne çıkan kurs oluştur
     *
     * @return static
     */
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }
} 