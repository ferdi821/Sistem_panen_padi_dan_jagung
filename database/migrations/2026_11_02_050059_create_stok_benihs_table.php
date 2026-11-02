<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stok_benihs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinsi_id')->constrained('provinsis')->onDelete('cascade');
            $table->foreignId('benih_id')->constrained('benihs')->onDelete('cascade');
            $table->decimal('jumlah_stok_kg', 12, 2)->default(0);
            $table->timestamps();

            // Memastikan 1 varietas benih per provinsi hanya punya 1 baris record persediaan
            $table->unique(['provinsi_id', 'benih_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('stok_benihs');
    }
};