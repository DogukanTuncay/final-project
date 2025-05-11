<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::table('user_notification_logs', function (Blueprint $table) {
            $table->string('status')->default('sent')->after('user_id');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('user_notification_logs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}; 