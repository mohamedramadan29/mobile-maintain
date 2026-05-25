<?php

namespace App\Models\dashboard;

use Illuminate\Database\Eloquent\Model;

class PcDevice extends Model
{
       protected $guarded = [];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
