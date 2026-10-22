<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('kode_pesanan')->unique();
            $table->foreignId('menu_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('nama_menu');
            $table->unsignedInteger('harga_satuan');
            $table->unsignedSmallInteger('jumlah');
            $table->unsignedBigInteger('total_harga');
            $table->string('nama_pelanggan');
            $table->string('telepon', 30);
            $table->text('catatan')->nullable();
            $table->string('metode_pembayaran', 30)->default('transfer_bank');
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
