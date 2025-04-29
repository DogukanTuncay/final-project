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
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url');
            $table->string('provider')->default('h5p'); // youtube, vimeo, custom
            $table->string('video_id')->nullable(); // YouTube veya Vimeo video ID'si
            $table->integer('duration')->nullable(); // Saniye cinsinden süre
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
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
