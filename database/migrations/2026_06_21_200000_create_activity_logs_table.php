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
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('user_name')->nullable();
                $table->string('user_email')->nullable();
                $table->string('role')->nullable();
                $table->string('category'); // e.g. 'auth', 'payment', 'preorder', 'settings', 'member'
                $table->string('action'); // e.g. 'login', 'logout', 'change_password', etc.
                $table->text('description');
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
