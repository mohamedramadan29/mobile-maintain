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
        Schema::create('invoice_speed_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            $table->foreignId('speed_id')->references('id')->on('speed_devices')->cascadeOnDelete();
            $table->string('problem_name');
            $table->tinyInteger('work')->default(0);
            $table->text('notes')->nullable();
            $table->text('after_check')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_speed_checks');
    }
};
