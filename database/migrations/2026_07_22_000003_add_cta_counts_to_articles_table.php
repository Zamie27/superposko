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
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedInteger('cta_wa_count')->default(0)->after('views_count');
            $table->unsignedInteger('cta_fb_count')->default(0)->after('cta_wa_count');
            $table->unsignedInteger('cta_ig_count')->default(0)->after('cta_fb_count');
            $table->unsignedInteger('cta_copy_count')->default(0)->after('cta_ig_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['cta_wa_count', 'cta_fb_count', 'cta_ig_count', 'cta_copy_count']);
        });
    }
};
