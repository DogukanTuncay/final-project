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
        Schema::table('missions', function (Blueprint $table) {
            // Polimorfik ilişki için alanlar
            $table->unsignedBigInteger('completable_id')->nullable()->after('id')->comment('Tamamlanması gereken öğenin ID\'si (Course, Chapter etc.)');
            $table->string('completable_type')->nullable()->after('completable_id')->comment('Tamamlanması gereken öğenin Model Sınıfı');

            // Görev gereksinimleri
            $table->unsignedInteger('required_amount')->default(1)->after('completable_type')->comment('Görevin tamamlanması için gereken miktar (örn: 5 ders)');
            $table->string('trigger_event')->nullable()->after('required_amount')->comment('İlerlemeyi tetikleyecek olayın adı (örn: LessonCompleted)');

            // Index'ler
            $table->index(['completable_id', 'completable_type']);
            $table->index('trigger_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropIndex('missions_completable_id_completable_type_index'); // Varsayılan index adı
            $table->dropIndex('missions_trigger_event_index'); // Varsayılan index adı

            $table->dropColumn([
                'completable_id',
                'completable_type',
                'required_amount',
                'trigger_event'
            ]);
        });
    }
}; 