<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Models\dashboard\Invoice;
use App\Models\dashboard\CheckText;
use App\Http\Controllers\Controller;
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
        return view('dashboard.show_invoice', compact('invoice', 'checks','programe_problems','speed_problems'));
    }
}
