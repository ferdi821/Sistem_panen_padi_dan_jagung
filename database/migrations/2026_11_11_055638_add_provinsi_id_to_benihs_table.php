<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('benihs', function (Blueprint $table) {
            if (!Schema::hasColumn('benihs', 'provinsi_id')) {
                $table->foreignId('provinsi_id')->nullable()->after('id')->constrained('provinsis')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('benihs', function (Blueprint $table) {
            $table->dropForeign(['provinsi_id']);
            $table->dropColumn('provinsi_id');
        });
    }
};