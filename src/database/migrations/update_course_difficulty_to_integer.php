<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Önce string olan difficulty değerlerini geçici bir sütuna kaydedelim
        Schema::table('courses', function (Blueprint $table) {
            $table->string('temp_difficulty')->nullable();
        });

        // Mevcut değerleri geçici sütuna taşı
        DB::statement('UPDATE courses SET temp_difficulty = difficulty');

        // Difficulty sütununu kaldır
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });

        // Yeni integer difficulty sütunu ekle
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('difficulty')->default(1)->comment('Zorluk seviyesi: 1=Kolay, 2=Orta, 3=Zor');
        });

        // Eski değerleri dönüştür
        DB::statement("UPDATE courses SET difficulty = CASE 
            WHEN temp_difficulty = 'easy' OR temp_difficulty = 'kolay' THEN 1
            WHEN temp_difficulty = 'medium' OR temp_difficulty = 'orta' THEN 2
            WHEN temp_difficulty = 'hard' OR temp_difficulty = 'zor' THEN 3
            ELSE 1 END");

        // Geçici sütunu kaldır
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('temp_difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Önce integer olan difficulty değerlerini geçici bir sütuna kaydedelim
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('temp_difficulty')->nullable();
        });

        // Mevcut değerleri geçici sütuna taşı
        DB::statement('UPDATE courses SET temp_difficulty = difficulty');

        // Difficulty sütununu kaldır
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });

        // Eski string formatında difficulty sütunu ekle
        Schema::table('courses', function (Blueprint $table) {
            $table->string('difficulty')->nullable();
        });

        // Değerleri dönüştür
        DB::statement("UPDATE courses SET difficulty = CASE 
            WHEN temp_difficulty = 1 THEN 'easy'
            WHEN temp_difficulty = 2 THEN 'medium'
            WHEN temp_difficulty = 3 THEN 'hard'
            ELSE 'easy' END");

        // Geçici sütunu kaldır
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('temp_difficulty');
        });
    }
}; 