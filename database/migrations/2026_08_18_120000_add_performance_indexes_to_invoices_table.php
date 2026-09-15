<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * جدول invoices لم يكن يحتوي على أي فهرس سوى المفتاح الأساسي ومفتاحي admins،
     * رغم أن كل الفلاتر والتقارير تعتمد على status و delivery_status و created_at.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // عدّادات لوحة التحكم والفلترة حسب الحالة (فهرس مُغطٍّ للعدّ)
            $table->index(['status', 'delivery_status'], 'invoices_status_delivery_index');

            // صفحتا "تم التسليم" و "لم يتم التسليم": فلترة ثم ترتيب تنازلي بالمعرف
            $table->index(['delivery_status', 'id'], 'invoices_delivery_id_index');

            // فلترة النطاق الزمني في صفحات الفواتير
            $table->index('created_at', 'invoices_created_at_index');

            // تقرير فواتير الفني: whereBetween على وقت بدء الصيانة
            $table->index('checkout_time', 'invoices_checkout_time_index');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_status_delivery_index');
            $table->dropIndex('invoices_delivery_id_index');
            $table->dropIndex('invoices_created_at_index');
            $table->dropIndex('invoices_checkout_time_index');
        });
    }
};
