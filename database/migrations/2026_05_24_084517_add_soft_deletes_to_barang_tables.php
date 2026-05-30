<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add soft delete support (deleted_at column) to the three core item tables.
     * This preserves historical data instead of permanently removing records.
     */
    public function up(): void
    {
        // Add soft deletes to barang (parent item table)
        if (!Schema::hasColumn('barang', 'deleted_at')) {
            Schema::table('barang', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to barang_temuan (found-item child records)
        if (!Schema::hasColumn('barang_temuan', 'deleted_at')) {
            Schema::table('barang_temuan', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to laporan_kehilangan (lost-item reports)
        if (!Schema::hasColumn('laporan_kehilangan', 'deleted_at')) {
            Schema::table('laporan_kehilangan', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('barang_temuan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('laporan_kehilangan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
