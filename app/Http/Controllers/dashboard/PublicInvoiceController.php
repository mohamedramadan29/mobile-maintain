<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Models\dashboard\Invoice;
use App\Models\dashboard\CheckText;
use App\Http\Controllers\Controller;

class PublicInvoiceController extends Controller
{
    public function PublicInvoice($id)
    {
        $invoice = Invoice::find($id);
        $checks = CheckText::all();
        return view('dashboard.show_invoice', compact('invoice','checks'));
    }
}
