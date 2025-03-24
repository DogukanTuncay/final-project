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
        Schema::create('multiple_choice_questions', function (Blueprint $table) {
            $table->id();
            $table->json('question')->comment('Soru metni');
            $table->json('feedback')->nullable()->comment('Geri bildirim metni');
            $table->unsignedInteger('points')->default(1)->comment('Soru puanı');
            $table->boolean('is_multiple_answer')->default(false)->comment('Birden fazla doğru cevap seçilebilir mi?');
            $table->boolean('shuffle_options')->default(true)->comment('Seçenekler karıştırılsın mı?');
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
        Schema::dropIfExists('multiple_choice_questions');
    }
};
