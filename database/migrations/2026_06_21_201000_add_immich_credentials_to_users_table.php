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
            if (! Schema::hasColumn('users', 'immich_api_key')) {
                $table->text('immich_api_key')->nullable()->after('subscription_expires_at');
            }
            if (! Schema::hasColumn('users', 'immich_email')) {
                $table->string('immich_email')->nullable()->after('immich_api_key');
            }
            if (! Schema::hasColumn('users', 'immich_password')) {
                $table->string('immich_password')->nullable()->after('immich_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'immich_api_key')) {
                $cols[] = 'immich_api_key';
            }
            if (Schema::hasColumn('users', 'immich_email')) {
                $cols[] = 'immich_email';
            }
            if (Schema::hasColumn('users', 'immich_password')) {
                $cols[] = 'immich_password';
            }

            if (count($cols) > 0) {
                $table->dropColumn($cols);
            }
        });
    }
};
