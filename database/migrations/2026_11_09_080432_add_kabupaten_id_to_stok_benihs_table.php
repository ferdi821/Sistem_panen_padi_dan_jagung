<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stok_benihs', function (Blueprint $table) {
            $table->foreignId('kabupaten_id')->nullable()->after('provinsi_id')->constrained('kabupatens')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('stok_benihs', function (Blueprint $table) {
            $table->dropForeign(['kabupaten_id']);
            $table->dropColumn('kabupaten_id');
        });
    }
};