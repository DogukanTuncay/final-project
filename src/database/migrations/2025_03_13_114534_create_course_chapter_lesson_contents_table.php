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
            $table->unsignedBigInteger('contentable_id');
            $table->string('contentable_type');
            $table->index(['contentable_id', 'contentable_type'], 'cclc_contentable_index');
            $table->integer('order')->default(0); // İçeriklerin sıralanması için
            $table->boolean('is_active')->default(true); // İçerik aktif mi?
            $table->json('meta_data')->nullable(); // İçerik türüne göre ek bilgileri JSON olarak tutmak için
            $table->timestamps();
            $table->softDeletes(); // Add soft delete column

            // Tekrarlanan içerikleri önlemek için
            $table->unique(['course_chapter_lesson_id', 'contentable_id', 'contentable_type'], 'cclc_lesson_contentable_unique');


            $table->foreign('course_chapter_lesson_id', 'cclc_lesson_foreign')->references('id')->on('course_chapter_lessons')->onDelete('cascade');
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
