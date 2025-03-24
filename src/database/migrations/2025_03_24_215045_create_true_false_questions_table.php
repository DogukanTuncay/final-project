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
        Schema::create('true_false_questions', function (Blueprint $table) {
            $table->id();
            $table->boolean('correct_answer')->comment('Doğru cevap (true veya false)');
            $table->json('true_text')->nullable()->comment('True seçeneği için özel metin');
            $table->json('false_text')->nullable()->comment('False seçeneği için özel metin');
            $table->json('true_feedback')->nullable()->comment('True seçeneği için geribildirim');
            $table->json('false_feedback')->nullable()->comment('False seçeneği için geribildirim');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('true_false_questions');
    }
};
