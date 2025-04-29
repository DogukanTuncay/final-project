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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_category_id')->constrained('story_categories')->onDelete('cascade');
            $table->json('title'); // Çevrilebilir başlık için
            $table->string('media_url')->nullable(); // Medya (resim/video) URL'si
            $table->text('content')->nullable(); // Metin içeriği
            $table->unsignedInteger('order_column')->default(0); // Sıralama için
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
}; 