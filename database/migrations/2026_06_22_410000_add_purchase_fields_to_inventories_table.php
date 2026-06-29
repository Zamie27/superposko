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
        Schema::table('inventories', function (Blueprint $table) {
            // source: 'member' = barang milik anggota, 'purchase' = dibeli dari kas
            $table->string('source')->default('member')->after('owner_id');
            $table->decimal('purchase_price', 15, 2)->nullable()->after('source');
            // If purchased from kas, link to the auto-created Finance record
            $table->foreignId('finance_id')->nullable()->after('purchase_price')->constrained('finances')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['finance_id']);
            $table->dropColumn(['source', 'purchase_price', 'finance_id']);
        });
    }
};
