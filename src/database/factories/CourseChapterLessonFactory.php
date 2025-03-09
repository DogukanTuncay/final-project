<?php

namespace Database\Factories;

use App\Models\CourseChapter;
use App\Models\CourseChapterLesson;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseChapterLesson>
 */
class CourseChapterLessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseChapterLesson::class;

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
            'course_chapter_id' => CourseChapter::factory(),
            'slug' => $slug,
            'name' => $name,
            'description' => $description,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'order' => $this->faker->numberBetween(1, 20),
            'is_active' => $this->faker->boolean(80),
            'thumbnail' => 'images/coursechapterlesson/default.jpg',
            'duration' => $this->faker->numberBetween(300, 3600)
        ];
    }
    
    /**
     * Belirli bir bölüme bağlı ders oluştur
     *
     * @param int $chapterId
     * @return static
     */
    public function forChapter(int $chapterId)
    {
        return $this->state(function (array $attributes) use ($chapterId) {
            return [
                'course_chapter_id' => $chapterId,
            ];
        });
    }
    
    /**
     * Aktif ders oluştur
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
     * Belirli bir sırada ders oluştur
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