<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Models\dashboard\Role;
use App\Models\dashboard\Admin;
use App\Models\dashboard\Invoice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\dashboard\ProblemCategory;

class WelcomeController extends Controller
{
    public function index()
    {
        if (Auth::guard('admin')->user()->type == 'فني') {
            $availableInvoices = Invoice::where('admin_repair_id', null)->orderBy('id', 'desc')->paginate(10);
        } else {
            $availableInvoices = Invoice::orderBy('id', 'desc')->limit(10)->get();
        }
        $invoices_count = Invoice::count();

        $AdminCount = Admin::count();
        $roles = Role::count();
        $problems = ProblemCategory::count();
        ####### Get Status Counts For Invoices
        $under_maintain_count = Invoice::where('status', 'تحت الصيانة')->count();
        $fixed_count = Invoice::where('status', 'تم الاصلاح')->count();
        $not_fixed_count = Invoice::where('status', 'لم يتم الاصلاح')->count();
        $roof_count = Invoice::where('status', 'رف الاستلام')->count();
        $pending = Invoice::where('status', 'معلق')->count();
        $delivery_count = Invoice::where('delivery_status', 1)->count();
        $undelivery_count = Invoice::where('delivery_status', 0)->count();
        return view("dashboard.welcome", compact('invoices_count', 'AdminCount', 'roles', 'problems', 'availableInvoices', 'under_maintain_count', 'fixed_count', 'not_fixed_count', 'roof_count', 'delivery_count', 'undelivery_count', 'pending'));
    }
}
