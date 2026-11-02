<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengujian_mutu_benihs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catatan_panen_id')->constrained('catatan_panens')->onDelete('cascade');
            $table->foreignId('benih_id')->constrained('benihs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_uji');
            $table->decimal('volume_sampel_kg', 10, 2);
            $table->decimal('kadar_air_persen', 5, 2);
            $table->decimal('daya_tumbuh_persen', 5, 2);
            $table->decimal('jumlah_lolos_kg', 10, 2)->default(0);
            $table->enum('status_kelayakan', ['proses', 'layak', 'tidak_layak'])->default('proses');
            $table->text('catatan_pengujian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pengujian_mutu_benihs');
    }
};