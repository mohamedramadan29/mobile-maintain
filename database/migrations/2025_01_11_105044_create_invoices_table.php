<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('invoice_number');
            $table->string('name');
            $table->string('phone');
            $table->json('problems');
            $table->string('title')->comment('problem');
            $table->text('description');
            $table->double('price');
            $table->string('date_delivery')->nullable();
            $table->string('time_delivery')->nullable();
            $table->string('status');
            $table->string('status_notes')->nullable();
            $table->string('signature');
            $table->foreignId('admin_recieved_id')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->foreignId('admin_repair_id')->nullable()->references('id')->on('admins')->nullOnDelete();
            $table->timestamp('checkout_time')->nullable();
            $table->timestamp('checkout_end_time')->nullable();
            $table->text('tech_notes')->nullable();
            $table->string('device_password_text')->nullable();
            $table->string('device_pattern')->nullable();
            $table->tinyInteger('client_connect')->nullable()->default(0);
            $table->text('client_connect_notes')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
