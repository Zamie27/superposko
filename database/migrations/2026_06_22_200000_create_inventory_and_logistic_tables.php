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
        // 1. Inventories (Inventaris Barang) Table
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->string('condition')->default('good'); // good (Bagus), damaged (Rusak), lost (Hilang)
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        // 2. Logistics (Logistik Bahan Habis Pakai) Table
        Schema::create('logistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('quantity'); // e.g. "5 kg", "2 Dus"
            $table->string('status')->default('sufficient'); // sufficient (Cukup), low (Menipis), out (Habis)
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logistics');
        Schema::dropIfExists('inventories');
    }
};
