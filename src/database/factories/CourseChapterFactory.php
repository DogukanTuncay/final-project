<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseChapter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseChapter>
 */
class CourseChapterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseChapter::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = [
            'tr' => $this->faker->sentence(2),
            'en' => $this->faker->sentence(2)
        ];
        
        $slug = Str::slug($name['en']);
        
        $description = [
            'tr' => $this->faker->paragraphs(2, true),
            'en' => $this->faker->paragraphs(2, true)
        ];
        
        $metaTitle = [
            'tr' => $this->faker->sentence(4),
            'en' => $this->faker->sentence(4)
        ];
        
        $metaDescription = [
            'tr' => $this->faker->paragraph(1),
            'en' => $this->faker->paragraph(1)
        ];
        
        return [
            'course_id' => Course::factory(),
            'slug' => $slug,
            'order' => $this->faker->numberBetween(1, 20),
            'is_active' => $this->faker->boolean(80),
            'name' => $name,
            'description' => $description,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription
        ];
    }
    
    /**
     * Belirli bir kursa bağlı bölüm oluştur
     *
     * @param int $courseId
     * @return static
     */
    public function forCourse(int $courseId)
    {
        return $this->state(function (array $attributes) use ($courseId) {
            return [
                'course_id' => $courseId,
            ];
        });
    }
    
    /**
     * Aktif bölüm oluştur
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
     * Belirli bir sırada bölüm oluştur
     *
     * @param int $order
     * @return static
     */
    public function order(int $order)
    {
        return $this->state(function (array $attributes) use ($order) {
            return [
                'order' => $order,
            ];
        });
    }
} 