<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('benihs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_benih', 30)->unique();
            $table->string('nama_benih', 100);
            $table->enum('komoditas', ['padi', 'jagung']);
            $table->string('varietas', 100);
            $table->enum('kelas_benih', ['BD', 'BP', 'BR'])->default('BR'); // BD=Benih Dasar, BP=Benih Pokok, BR=Benih Sebar
            $table->string('satuan', 20)->default('kg');
            $table->decimal('stok_minimum', 10, 2)->default(100.00);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('benihs');
    }
};