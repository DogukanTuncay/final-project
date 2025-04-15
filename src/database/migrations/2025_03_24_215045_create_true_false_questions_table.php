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
            $table->json('question')->comment('Soru metni');
            $table->boolean('correct_answer')->comment('Doğru cevap (true veya false)');
            $table->json('custom_text')->nullable()->comment('True/False için özel metinler');
            $table->json('feedback')->nullable()->comment('Geribildirim');
            $table->unsignedInteger('points')->default(1)->comment('Soru puanı');
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
        Schema::dropIfExists('true_false_questions');
    }
};
