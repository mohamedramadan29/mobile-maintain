<?php

namespace App\Models\dashboard;

use App\Models\dashboard\InvoicePrograneCheck;
use App\Models\dashboard\InvoiceSpeedCheck;
use App\Models\dashboard\InvoiceArchive;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $guarded = [];
    protected $appends = ['repair_timer_status'];

    public function getRepairTimerStatusAttribute()
    {
        if (!$this->checkout_time) {
            return 'لم يبدأ';
        }

        $expectedTime = $this->expected_repair_time ?? 30; // المدة الطبيعية بالدقائق
        $endTime = $this->checkout_end_time ? \Carbon\Carbon::parse($this->checkout_end_time) : now();
        $startTime = \Carbon\Carbon::parse($this->checkout_time);

        $durationInMinutes = $startTime->diffInMinutes($endTime);

        if ($durationInMinutes <= $expectedTime) {
            return 'نجاح';
        }

        return 'تجاوز الوقت';
    }

    public function Problems()
    {
        return $this->hasMany(ProblemCategory::class, 'problems');
    }

    public function files()
    {
        return $this->hasMany(InvoiceImage::class, 'invoice_id');
    }

    public function Recieved()
    {
        return $this->belongsTo(Admin::class, 'admin_recieved_id');
    }
    public function Technical()
    {
        return $this->belongsTo(Admin::class, 'admin_repair_id');
    }

    /**
     * الموظف الذي أتم تسليم الجهاز للعميل.
     */
    public function Delivered()
    {
        return $this->belongsTo(Admin::class, 'delivered_by');
    }

    /**
     * صور/فيديوهات حالة الجهاز بعد الصيانة (المرفقات المطلوبة عند التسليم).
     */
    public function statusFiles()
    {
        return $this->hasMany(InvoiceImage::class, 'invoice_id')->where('file_type', 'status');
    }

    /**
     * حالات الإصلاح المقبولة كشرط أساسي للتسليم.
     */
    public const REPAIR_STATUSES = ['تم الاصلاح', 'لم يتم الاصلاح'];

    /**
     * هل حُدِّدت حالة الإصلاح (تم / لم يتم)؟
     */
    public function hasRepairStatus(): bool
    {
        return in_array($this->status, self::REPAIR_STATUSES, true);
    }

    /**
     * هل تتوفر صورة أو فيديو لحالة الجهاز؟
     */
    public function hasStatusMedia(): bool
    {
        return $this->statusFiles()->exists();
    }

    /**
     * جهاز مكتمل = له صورة/فيديو حالة وحالة إصلاح محددة، فيُجهَّز للتسليم مباشرة.
     */
    public function isReadyForDelivery(): bool
    {
        return $this->hasRepairStatus() && $this->hasStatusMedia();
    }

    public function Steps()
    {
        return $this->hasMany(InvoiceSteps::class, 'invoice_id');
    }

    ############# Invoice Check ###############

    public function checkResults()
    {
        return $this->hasMany(InvoiceCheck::class, 'invoice_id');
    }

    ############# Speed  Check ###############

    public function speedResults()
    {
        return $this->hasMany(InvoiceSpeedCheck::class, 'invoice_id');
    }

    ############# Pograme Check ###############

    public function programeResults()
    {
        return $this->hasMany(InvoicePrograneCheck::class, 'invoice_id');
    }

    ############# Sony  Check ###############

    public function sonyResults()
    {
        return $this->hasMany(InvoiceSonyCheck::class, 'invoice_id');
    }

    ############# Pc  Check ###############

    public function pcResults()
    {
        return $this->hasMany(InvoicePcCheck::class, 'invoice_id');
    }

    public function priceDetails()
    {
        return $this->hasMany(PriceDetail::class, 'invoice_id');
    }

    /**
     * Get the archives for this invoice.
     */
    public function archives()
    {
        return $this->hasMany(InvoiceArchive::class, 'invoice_id');
    }

    /**
     * Check if the invoice is archived.
     */
    public function isArchived()
    {
        return $this->archives()->where('status', 'archived')->exists();
    }

    /**
     * Get the latest archive record.
     */
    public function latestArchive()
    {
        return $this->archives()->latest()->first();
    }
}
