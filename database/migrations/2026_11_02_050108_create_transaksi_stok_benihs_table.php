<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transaksi_stok_benihs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_benih_id')->constrained('stok_benihs')->onDelete('cascade');
            $table->foreignId('pengujian_mutu_id')->nullable()->constrained('pengujian_mutu_benihs')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('jenis_transaksi', ['masuk', 'keluar']);
            $table->string('sumber_tujuan', 150);
            $table->decimal('jumlah_kg', 10, 2);
            $table->decimal('stok_sebelum', 12, 2);
            $table->decimal('stok_sesudah', 12, 2);
            $table->date('tanggal_transaksi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('transaksi_stok_benihs');
    }
};