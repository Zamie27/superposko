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
        if (! Schema::hasTable('proker_documents')) {
            Schema::create('proker_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('host_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('category'); // 'proposal', 'lpj', 'perizinan', 'notulensi', 'desain', 'lainnya'
                $table->string('file_path');
                $table->string('file_name');
                $table->unsignedBigInteger('file_size');
                $table->string('mime_type');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proker_documents');
    }
};
