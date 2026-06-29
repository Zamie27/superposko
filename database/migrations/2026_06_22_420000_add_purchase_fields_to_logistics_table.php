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
        Schema::table('logistics', function (Blueprint $table) {
            // source: 'member' = barang milik anggota, 'purchase' = dibeli dari kas
            $table->string('source')->default('member')->after('host_id');
            $table->foreignId('owner_id')->nullable()->after('source')->constrained('users')->nullOnDelete();
            $table->decimal('purchase_price', 15, 2)->nullable()->after('owner_id');
            // If purchased from kas, link to auto-created Finance record
            $table->foreignId('finance_id')->nullable()->after('purchase_price')->constrained('finances')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logistics', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['finance_id']);
            $table->dropColumn(['source', 'owner_id', 'purchase_price', 'finance_id']);
        });
    }
};
