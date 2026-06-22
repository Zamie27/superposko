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
        if (! Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
                $table->string('name');
                $table->string('role_title');
                $table->string('category'); // 'aparat_desa', 'kesehatan', 'keamanan', 'akademik', 'mitra'
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
