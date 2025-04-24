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
        Schema::create('matching_questions', function (Blueprint $table) {
            $table->id();
            $table->json('question')->comment('Soru metni');
            $table->boolean('shuffle_items')->default(true)->comment('Eşleştirme öğelerini karıştır');
            $table->unsignedInteger('points')->default(1)->comment('Soru puanı');
            $table->json('feedback')->nullable()->comment('Geribildirim');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matching_questions', function (Blueprint $table) {
             $table->dropSoftDeletes();
        });
        Schema::dropIfExists('matching_questions');
    }
};
