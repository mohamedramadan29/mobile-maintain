<?php

namespace App\Models\dashboard;

use Illuminate\Database\Eloquent\Model;

class InvoiceCheck extends Model
{
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
