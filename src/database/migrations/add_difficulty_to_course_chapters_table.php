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
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->unsignedTinyInteger('difficulty')->default(1)->comment('Zorluk seviyesi: 1=Kolay, 2=Orta, 3=Zor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_chapters', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });
    }
}; 