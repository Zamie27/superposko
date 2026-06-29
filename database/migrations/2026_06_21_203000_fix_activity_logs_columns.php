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
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('activity_logs', 'user_name')) {
                    $table->string('user_name')->nullable()->after('user_id');
                }
                if (! Schema::hasColumn('activity_logs', 'user_email')) {
                    $table->string('user_email')->nullable()->after('user_name');
                }
                if (! Schema::hasColumn('activity_logs', 'role')) {
                    $table->string('role')->nullable()->after('user_email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('activity_logs', 'user_name')) {
                    $cols[] = 'user_name';
                }
                if (Schema::hasColumn('activity_logs', 'user_email')) {
                    $cols[] = 'user_email';
                }
                if (Schema::hasColumn('activity_logs', 'role')) {
                    $cols[] = 'role';
                }

                if (count($cols) > 0) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
