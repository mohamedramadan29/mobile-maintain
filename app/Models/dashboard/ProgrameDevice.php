<?php

namespace App\Models\dashboard;

use Illuminate\Database\Eloquent\Model;

class ProgrameDevice extends Model
{
    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
