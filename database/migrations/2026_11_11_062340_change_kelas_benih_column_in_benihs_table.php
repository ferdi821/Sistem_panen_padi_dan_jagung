<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('benihs', function (Blueprint $table) {
            // Mengubah kelas_benih menjadi string/varchar 50 karakter agar muat nilai BS, BR, BP, BD dll.
            $table->string('kelas_benih', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('benihs', function (Blueprint $table) {
            $table->string('kelas_benih', 10)->change();
        });
    }
};