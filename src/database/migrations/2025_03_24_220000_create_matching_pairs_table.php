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
        Schema::create('matching_pairs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('matching_question_id');
            $table->json('left_item')->comment('Sol taraftaki öğe');
            $table->json('right_item')->comment('Sağ taraftaki öğe');
            $table->unsignedInteger('order')->default(0)->comment('Sıralama');
            $table->timestamps();
            
            $table->foreign('matching_question_id')
                  ->references('id')
                  ->on('matching_questions')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matching_pairs');
    }
}; 