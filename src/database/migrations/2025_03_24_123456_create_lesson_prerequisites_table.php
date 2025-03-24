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
        Schema::create('lesson_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('course_chapter_lessons')->onDelete('cascade');
            $table->foreignId('prerequisite_lesson_id')->constrained('course_chapter_lessons')->onDelete('cascade');
            $table->timestamps();

            // Aynı ders-ön koşul ikilisinin tekrar oluşturulmasını engellemek için
            $table->unique(['lesson_id', 'prerequisite_lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_prerequisites');

       
    }
}; 