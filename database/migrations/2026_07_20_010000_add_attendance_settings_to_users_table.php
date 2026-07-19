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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('attendance_lat', 10, 8)->nullable()->after('group_number');
            $table->decimal('attendance_lng', 11, 8)->nullable()->after('attendance_lat');
            $table->integer('attendance_radius')->nullable()->after('attendance_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['attendance_lat', 'attendance_lng', 'attendance_radius']);
        });
    }
};
