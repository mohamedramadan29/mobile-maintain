<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Models\dashboard\Invoice;
use App\Models\dashboard\CheckText;
use App\Http\Controllers\Controller;
use App\Models\dashboard\SpeedDevice;
use App\Models\dashboard\ProgrameDevice;
use App\Models\dashboard\ProblemCategory;
use App\Models\dashboard\InvoiceMoreCheck;
use App\Models\dashboard\SpeedProblemCategory;
use App\Models\dashboard\ProgrameProblemCategory;

class PublicInvoiceController extends Controller
{
    public function PublicInvoice($id)
    {
        $invoice = Invoice::find($id);
        $checks = CheckText::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $invoice_more_checks = InvoiceMoreCheck::all();
        $problems = ProblemCategory::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        return view('dashboard.show_invoice', compact('invoice',
        'problems',
        'speed_devices',
        'programe_devices',
        'checks','programe_problems','speed_problems','invoice_more_checks'));
    }
}
