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

        Schema::table('fasilitas', function (Blueprint $table) {
            if (!Schema::hasColumn('fasilitas', 'harga')) {
                $table->decimal('harga', 15, 2)->default(0)->after('nama_fasilitas');
            }
        });

        Schema::create('pemesanan_fasilitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanan')->onDelete('cascade');
            $table->foreignId('fasilitas_id')->constrained('fasilitas')->onDelete('cascade');
            $table->decimal('harga_snap', 15, 2);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
