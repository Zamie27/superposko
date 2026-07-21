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
            $table->string('posko_village')->nullable()->after('kkn_address');
            $table->string('posko_district')->nullable()->after('posko_village');
            $table->string('posko_regency')->nullable()->after('posko_district');
            $table->string('posko_province')->nullable()->after('posko_regency');
            $table->string('posko_postal_code')->nullable()->after('posko_province');
            $table->string('posko_logo_url')->nullable()->after('posko_postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'posko_village',
                'posko_district',
                'posko_regency',
                'posko_province',
                'posko_postal_code',
                'posko_logo_url',
            ]);
        });
    }
};
