<?php

namespace App\Http\Controllers\dashboard;

use Exception;
use Illuminate\Http\Request;
use App\Models\dashboard\Invoice;
use App\Http\Traits\Message_Trait;
use App\Http\Traits\Upload_Images;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\dashboard\CheckText;
use Illuminate\Support\Facades\Auth;
use App\Models\dashboard\InvoiceImage;
use App\Models\dashboard\InvoiceSteps;
use Illuminate\Support\Facades\Redirect;
use App\Models\dashboard\ProblemCategory;

class TechInvoicesController extends Controller
{
    use Message_Trait;
    use Upload_Images;


    public function index()
    {
        $invoices = Invoice::where('admin_repair_id', Auth::guard('admin')->user()->id)->orderBy('id', 'desc')->paginate(10);
        return view('dashboard.tech_invoices.index', compact('invoices'));
    }

    public function available()
    {
        $invoices = Invoice::where('admin_repair_id', null)->orderBy('id', 'desc')->paginate(10);
        return view('dashboard.tech_invoices.available', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = Invoice::find($id);
        $problems = ProblemCategory::all();
        $checks = CheckText::all();
        return view('dashboard.tech_invoices.show', compact('invoice', 'problems','checks'));
    }

    public function checkout($id)
    {
        ############# Check If This User Have More Invoice Or Not ##############
        try {
            DB::beginTransaction();
            $invoices = Invoice::where('admin_repair_id', Auth::guard('admin')->user()->id)->where('status', 'تحت الصيانة')->count();
            $admin = Auth::guard('admin')->user();
            $available_number = $admin->device_nums;
            if ($invoices >= $available_number) {
                return $this->Error_message('لقد تجاوزت العدد المسموح به للعمل في نفس الوقت ');
            }
            $invoice = Invoice::find($id);
            $invoice->admin_repair_id = Auth::guard('admin')->user()->id;
            $invoice->status = 'تحت الصيانة';
            $invoice->checkout_time = now();
            $invoice->save();

            ############# Add Invoice Step ###############
            $invoice_step = new InvoiceSteps();
            $invoice_step->invoice_id = $invoice->id;
            $invoice_step->admin_id = Auth::id();
            $invoice_step->step_details = ' تم بدء الصيانة علي الجهاز';
            $invoice_step->save();

            DB::commit();
            return $this->success_message('تم بدأ العمل علي الجهاز  بنجاح');
        } catch (Exception $e) {
            return $this->exception_message($e);
        }
    }

    ################ Update After Repair ##################
    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if ($request->isMethod('post')) {
            try {
                DB::beginTransaction();
                $invoice->status = $request->status;
                $invoice->tech_notes = $request->tech_notes;
                $invoice->checkout_end_time = now();
                $invoice->save();
                ############# Add Invoice Step ###############
                $invoice_step = new InvoiceSteps();
                $invoice_step->invoice_id = $invoice->id;
                $invoice_step->admin_id = Auth::id();
                $invoice_step->step_details = '  تم تحديث حالة الجهاز الي   . $request->status;';
                $invoice_step->save();
                DB::commit();
                return $this->success_message('تم تحديث حالة الجهاز بنجاح');
            } catch (Exception $e) {
                return $this->exception_message($e);
            }
        }
        $problems = ProblemCategory::all();
        $checks = CheckText::all();
        return view('dashboard.tech_invoices.update', compact('invoice', 'problems','checks'));
    }

    public function addfile(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        try {
            if ($request->hasFile('file')) {
                $filename = $this->saveImage($request->file('file'), public_path('assets/uploads/invoices_files'));
            }
            $file = new InvoiceImage();
            $file->invoice_id = $id;
            $file->image = $filename;
            $file->user_upload = Auth::id();
            $file->title = $request->title;
            $file->description = $request->description;
            $file->price = $request->price;
            $file->save();
            return $this->success_message(' تم اضافة المرفق بنجاح  ');
        } catch (Exception $e) {
            return $this->exception_message($e);
        }
    }

}
