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
        Schema::create('story_categories', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // Translatable
            $table->string('slug')->unique();
            $table->string('image_url')->nullable(); // Resim URL'si eklendi
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes(); // Soft delete ekleyelim
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_categories');
    }
};
