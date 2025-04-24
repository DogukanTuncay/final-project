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
        Schema::create('fill_in_the_blanks', function (Blueprint $table) {
            $table->id();
            $table->json('question')->comment('Çok dilli soru');
            $table->json('answers')->comment('Çok dilli cevaplar (JSON formatında)');
            $table->unsignedInteger('points')->default(1)->comment('Soru puanı');
            $table->json('feedback')->nullable()->comment('Geribildirim');
            $table->boolean('case_sensitive')->default(false)->comment('Büyük/küçük harf duyarlı mı?');
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
        
        Schema::dropIfExists('fill_in_the_blanks');
    }
};
