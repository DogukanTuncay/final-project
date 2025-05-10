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
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('xp_reward');
            $table->string('type')->default('one_time'); // one_time, daily, weekly, etc.
            $table->json('title');
            $table->json('description')->nullable();
            $table->json('requirements')->nullable(); // {"type": "comment_count", "value": 1}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
