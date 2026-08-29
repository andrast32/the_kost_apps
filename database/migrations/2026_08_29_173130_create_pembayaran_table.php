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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            // relasi pemesanan
            $table->foreignID('id_pemesanan')->constrained('pemesanan')->restrictOnDelete();

            // relasi pembayaran
            $table->dateTime('tanggal_bayar')->nullable();
            $table->decimal('jumlah_bayar', 15, 2);

            $table->string('metode_pembayaran')->nullable();

            $table->string('transaction_id')->nullable()->unique();

            $table->string('bukti')->nullable();

            $table->enum('status', ['Menunggu', 'Lunas'])->default('Menunggu');

            $table->foreignId('id_admin')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
