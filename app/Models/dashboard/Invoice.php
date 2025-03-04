<?php

namespace App\Models\dashboard;

use App\Models\dashboard\InvoicePrograneCheck;
use App\Models\dashboard\InvoiceSpeedCheck;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

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

}
