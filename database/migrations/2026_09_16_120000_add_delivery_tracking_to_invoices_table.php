<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * الأعمدة محمية بـ hasColumn لأن جدول invoices عُدّل يدوياً سابقاً
     * (مثال: delivery_status موجود في قاعدة البيانات بدون migration).
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'delivered_by')) {
                $table->foreignId('delivered_by')->nullable()->after('admin_repair_id')
                    ->references('id')->on('admins')->nullOnDelete()
                    ->comment('الموظف الذي أتم التسليم');
            }
            if (!Schema::hasColumn('invoices', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('actual_time_delivery')
                    ->comment('تاريخ ووقت التسليم الفعلي');
            }
            if (!Schema::hasColumn('invoices', 'delivery_without_image')) {
                $table->tinyInteger('delivery_without_image')->default(0)->after('delivered_at')
                    ->comment('تم التسليم بتأكيد الموظف بدون إرفاق صورة');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'delivered_by')) {
                $table->dropForeign(['delivered_by']);
                $table->dropColumn('delivered_by');
            }
            foreach (['delivered_at', 'delivery_without_image'] as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
