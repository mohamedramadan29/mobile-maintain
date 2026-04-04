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
        Schema::create('invoice_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('archived_by')->constrained('admins')->onDelete('cascade');
            $table->date('archive_date');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['archived', 'restored'])->default('archived');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['archive_date', 'status']);
            $table->index('archived_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_archives');
    }
};
