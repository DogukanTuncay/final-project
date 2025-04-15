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
        Schema::create('short_answer_questions', function (Blueprint $table) {
            $table->id();
            $table->json('question')->comment('Soru metni');
            $table->json('allowed_answers')->comment('Kabul edilecek cevaplar');
            $table->boolean('case_sensitive')->default(false)->comment('Büyük/küçük harf duyarlı mı?');
            $table->unsignedInteger('max_attempts')->nullable()->comment('Maksimum deneme sayısı');
            $table->unsignedInteger('points')->default(1)->comment('Soru puanı');
            $table->json('feedback')->nullable()->comment('Geribildirim');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('short_answer_questions');
    }
};
