<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change to string to be flexible
        Schema::table('barang', function (Blueprint $table) {
            $table->string('status', 50)->nullable()->change();
        });

        Schema::table('barang_temuan', function (Blueprint $table) {
            $table->string('status', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If we need to go back, we can, but usually we don't
    }
};
