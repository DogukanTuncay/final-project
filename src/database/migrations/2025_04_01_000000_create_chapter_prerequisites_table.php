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
        Schema::create('chapter_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('course_chapters')->onDelete('cascade');
            $table->foreignId('prerequisite_chapter_id')->constrained('course_chapters')->onDelete('cascade');
            $table->timestamps();

            // Aynı chapter-prerequisite ikilisinin tekrar oluşturulmasını engellemek için
            $table->unique(['chapter_id', 'prerequisite_chapter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_prerequisites');
    }
}; 