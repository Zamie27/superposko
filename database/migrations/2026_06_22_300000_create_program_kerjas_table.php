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
        Schema::create('program_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('category'); // e.g. fisik, non_fisik, keagamaan, kesehatan, pendidikan, tambahan
            $table->text('description')->nullable();
            $table->integer('progress')->default(0); // 0 to 100
            $table->decimal('budget', 12, 2)->default(0);
            $table->string('status')->default('planned'); // planned, in_progress, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_kerjas');
    }
};
