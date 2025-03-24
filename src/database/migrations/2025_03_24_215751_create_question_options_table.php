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
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->json('text')->comment('Seçenek metni');
            $table->boolean('is_correct')->default(false)->comment('Doğru cevap mı?');
            $table->unsignedInteger('order')->default(0)->comment('Seçenek sırası');
            $table->json('feedback')->nullable()->comment('Bu seçenek için özel geribildirim');
            $table->timestamps();
            
            $table->foreign('question_id')->references('id')->on('multiple_choice_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
