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
        Schema::create('course_chapter_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_chapter_id')->constrained('course_chapters')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('thumbnail')->nullable();
            $table->integer('duration')->default(0)->comment('Ders süresi (saniye)');
            $table->timestamps();
        });
    }

    /**
     */
    public function down(): void
    {
        Schema::dropIfExists('course_chapter_lessons');
    }
};
