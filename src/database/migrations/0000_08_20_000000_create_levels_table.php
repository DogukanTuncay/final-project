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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->integer('level_number')->unique();
            $table->json('title');
            $table->json('description')->nullable();
            $table->integer('min_xp');
            $table->integer('max_xp');
            $table->string('icon')->nullable();
            $table->string('color_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('required_exp')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('levels');
    }
}; 