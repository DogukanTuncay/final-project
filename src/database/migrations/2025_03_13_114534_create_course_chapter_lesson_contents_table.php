<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('course_chapter_lesson_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_chapter_lesson_id')->constrained()->onDelete('cascade');
            $table->morphs('contentable'); // Polimorfik ilişki için contentable_id ve contentable_type sütunlar
            $table->integer('order')->default(0); // İçeriklerin sıralanması için
            $table->boolean('is_active')->default(true); // İçerik aktif mi?
            $table->text('meta_data')->nullable(); // İçerik türüne göre ek bilgileri JSON olarak tutmak için
            $table->timestamps();

            // Tekrarlanan içerikleri önlemek için
            $table->unique(['course_chapter_lesson_id', 'contentable_id', 'contentable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_chapter_lesson_contents');
    }
};
