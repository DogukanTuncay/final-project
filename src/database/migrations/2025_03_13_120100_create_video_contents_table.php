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
        Schema::create('video_contents', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Çok dilli başlık
            $table->json('description')->nullable(); // Çok dilli açıklama
            $table->string('video_url'); // Video URL'i
            $table->string('provider')->default('youtube'); // Video sağlayıcısı (youtube, vimeo, etc.)
            $table->integer('duration')->nullable(); // Video süresi (saniye)
            $table->string('thumbnail')->nullable(); // Video kapak resmi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_contents');
    }
}; 