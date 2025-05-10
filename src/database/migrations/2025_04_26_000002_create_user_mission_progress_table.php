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
        Schema::create('user_mission_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mission_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('current_amount')->default(0)->comment('Kullanıcının mevcut ilerleme miktarı');
            $table->timestamp('completed_at')->nullable()->comment('Görevin tamamlandığı zaman');
            $table->unsignedInteger('xp_reward')->default(0)->comment('Kullanıcıya verilen XP ödülü');
            $table->timestamps(); // created_at ve updated_at   

            // Bir kullanıcının bir görevi sadece bir kez takip edebilmesi için
            $table->unique(['user_id', 'mission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_mission_progress');
    }
}; 