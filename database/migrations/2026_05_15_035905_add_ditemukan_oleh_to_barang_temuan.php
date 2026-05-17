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
        Schema::table('barang_temuan', function (Blueprint $table) {
            $table->string('ditemukan_oleh', 100)->nullable()->after('tanggal_diunggah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_temuan', function (Blueprint $table) {
            $table->dropColumn('ditemukan_oleh');
        });
    }
};
