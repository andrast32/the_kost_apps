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
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kamar', 50)->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 10, 2);
            $table->enum('status', ['Kosong', 'Terisi', 'Dalam Perbaikan'])->default('Kosong');
            $table->enum('khusus', ['Laki-Laki', 'Perempuan', 'Keluarga']);
            $table->string('foto')->nullable();
            $table->text('slug_kamar');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
