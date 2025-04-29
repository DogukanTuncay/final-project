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
        // Story tablosuna image ve images alanları ekleme
        if (!Schema::hasColumn('stories', 'image')) {
            Schema::table('stories', function (Blueprint $table) {
                $table->string('image')->nullable()->after('content');
                $table->json('images')->nullable()->after('image');
            });
        }
        
        // StoryCategory tablosunda image_url alanını image olarak değiştirme
        if (Schema::hasColumn('story_categories', 'image_url') && !Schema::hasColumn('story_categories', 'image')) {
            Schema::table('story_categories', function (Blueprint $table) {
                $table->renameColumn('image_url', 'image');
            });
        } else if (!Schema::hasColumn('story_categories', 'image')) {
            Schema::table('story_categories', function (Blueprint $table) {
                $table->string('image')->nullable()->after('slug');
            });
        }
        
        // CourseChapter tablosuna image ve images alanları ekleme
        if (!Schema::hasColumn('course_chapters', 'image')) {
            Schema::table('course_chapters', function (Blueprint $table) {
                $table->string('image')->nullable()->after('meta_description');
                $table->json('images')->nullable()->after('image');
            });
        }

        // Badge tablosu image alanı eğer yoksa ekleme
        if (!Schema::hasColumn('badges', 'image')) {
            Schema::table('badges', function (Blueprint $table) {
                $table->string('image')->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Story tablosundan image ve images alanları silme
        if (Schema::hasColumn('stories', 'image') && Schema::hasColumn('stories', 'images')) {
            Schema::table('stories', function (Blueprint $table) {
                $table->dropColumn(['image', 'images']);
            });
        }
        
        // StoryCategory tablosunda image alanını image_url olarak değiştirme
        if (Schema::hasColumn('story_categories', 'image') && !Schema::hasColumn('story_categories', 'image_url')) {
            Schema::table('story_categories', function (Blueprint $table) {
                $table->renameColumn('image', 'image_url');
            });
        } else if (Schema::hasColumn('story_categories', 'image')) {
            Schema::table('story_categories', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
        
        // CourseChapter tablosundan image ve images alanları silme
        if (Schema::hasColumn('course_chapters', 'image') && Schema::hasColumn('course_chapters', 'images')) {
            Schema::table('course_chapters', function (Blueprint $table) {
                $table->dropColumn(['image', 'images']);
            });
        }

        // Badge tablosu image alanı varsa silme
        if (Schema::hasColumn('badges', 'image')) {
            Schema::table('badges', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
}; 