<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wilayahs', function (Blueprint $table) {
            if (!Schema::hasColumn('wilayahs', 'provinsi_id')) {
                $table->foreignId('provinsi_id')->nullable()->after('kode_wilayah')->constrained('provinsis')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('wilayahs', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropColumn('provinsi_id');
        });
    }
};