<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create('user_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('notification_type', 50); // Bildirim türü (login_streak, course_completion, vb.)
            $table->string('title'); // Bildirim başlığı
            $table->text('message'); // Bildirim mesajı
            $table->timestamp('sent_at'); // Gönderim zamanı
            $table->timestamps();
            
            // İndeksler
            $table->index(['user_id', 'notification_type']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_logs');
    }
}; 