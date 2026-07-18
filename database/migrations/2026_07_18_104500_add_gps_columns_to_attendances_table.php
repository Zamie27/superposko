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
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('immich_asset_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('village')->nullable()->after('longitude');
            $table->string('district')->nullable()->after('village');
            $table->string('regency')->nullable()->after('district');
            $table->string('province')->nullable()->after('regency');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alfa'])->default('Hadir')->after('province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'village',
                'district',
                'regency',
                'province',
                'status',
            ]);
        });
    }
};
