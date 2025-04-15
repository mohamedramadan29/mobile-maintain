<?php

namespace App\Http\Controllers\dashboard;

use Exception;
use Illuminate\Http\Request;
use App\Jobs\SendReviewMessage;
use App\Models\dashboard\Invoice;
use App\Models\dashboard\Message;
use App\Http\Traits\Message_Trait;
use App\Http\Traits\Upload_Images;
use Illuminate\Support\Facades\DB;
use App\Models\dashboard\CheckText;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\dashboard\SpeedDevice;
use App\Models\dashboard\InvoiceImage;
use App\Models\dashboard\InvoiceSteps;
use App\Models\dashboard\ProgrameDevice;
use Illuminate\Support\Facades\Redirect;
use App\Models\dashboard\ProblemCategory;
use App\Models\dashboard\InvoiceSpeedCheck;
use App\Models\dashboard\InvoicePrograneCheck;

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
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        return view('dashboard.tech_invoices.show', compact('invoice', 'problems', 'checks', 'speed_devices', 'programe_devices'));
    }

    public function checkout(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if ($request->isMethod('post')) {
            $message_temp = Message::where('message_type', 'تحت الصيانة')->value('template_text');
            // dd($message_temp);
            ############# Check If This User Have More Invoice Or Not ##############
            try {
                DB::beginTransaction();
                $invoices = Invoice::where('admin_repair_id', Auth::guard('admin')->user()->id)->where('status', 'تحت الصيانة')->count();
                $admin = Auth::guard('admin')->user();
                $available_number = $admin->device_nums;
                if ($invoices >= $available_number) {
                    return $this->Error_message('لقد تجاوزت العدد المسموح به للعمل في نفس الوقت ');
                }

                $invoice->admin_repair_id = Auth::guard('admin')->user()->id;
                $invoice->status = 'تحت الصيانة';
                $invoice->checkout_time = now();
                $invoice->save();

                ########## Send Message To Client

                $invoice_link = url('dashboard/invoice/view/' . $invoice->id);
                $new_phone = preg_replace('/^0/', '', $invoice->phone);
                // إضافة رمز البلد +966
                $new_phone = '966' . $new_phone;

                $message = str_replace(
                    ['{name}', '{status}', '{invoice_link}'],
                    [$invoice->name, $invoice->status, $invoice_link],
                    $message_temp
                );
                // dd($message);
                // تعريف المتغير
                $params = array(
                    'instanceid' => '138796',
                    'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
                    'phone' => $new_phone,
                    'body' => $message,
                );
                $queryString = http_build_query($params); // تحويل المصفوفة إلى سلسلة نصية
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.4whats.net/sendMessage/?" . $queryString, // إضافة سلسلة الاستعلام إلى عنوان URL
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                ############# Add Invoice Step ###############
                $invoice_step = new InvoiceSteps();
                $invoice_step->invoice_id = $invoice->id;
                $invoice_step->admin_id = Auth::id();
                $invoice_step->step_details = ' تم بدء الصيانة علي الجهاز';
                $invoice_step->save();

                DB::commit();
                // return $this->success_message('تم بدأ العمل علي الجهاز  بنجاح');
                return Redirect::route('dashboard.tech_invoices.index')->with(['تم بدأ العمل علي الجهاز  بنجاح']);
            } catch (Exception $e) {
                return $this->exception_message($e);
            }
        }
        return view('dashboard.tech_invoices.checkout', compact('invoice'));

    }

    ################ Update After Repair ##################
    public function update(Request $request, $id)
    {
        $message_temp = Message::where('message_type', 'استلام الجهاز')->value('template_text');
        //dd($message_temp);
        $invoice = Invoice::find($id);
        if ($request->isMethod('post')) {
            //dd($request->all());
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
                ############### Send Message To Client If Device Correct Or Not Or Device Status  ####################

                ########### Send Message To WhatsApp
                // إنشاء رابط عام للفاتورة

                ########## Send Message To Client

                $invoice_link = url('dashboard/invoice/view/' . $invoice->id);
                $new_phone = preg_replace('/^0/', '', $invoice->phone);
                // إضافة رمز البلد +966
                $new_phone = '966' . $new_phone;

                // $new_phone = '201011642731';

                $message = str_replace(
                    ['{name}', '{status}', '{invoice_link}'],
                    [$invoice->name, $invoice->status, $invoice_link],
                    $message_temp
                );
                // dd($message);

                // تعريف المتغير
                $params = array(
                    'instanceid' => '138796',
                    'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
                    'phone' => $new_phone,
                    'body' => $message,
                );
                $queryString = http_build_query($params); // تحويل المصفوفة إلى سلسلة نصية
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.4whats.net/sendMessage/?" . $queryString, // إضافة سلسلة الاستعلام إلى عنوان URL
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($request->status === "تم الاصلاح") {
                    // جدولة إرسال رسالة التقييم بعد 20 دقيقة
                    SendReviewMessage::dispatch($invoice)->delay(now()->addMinutes(20));
                }
                DB::commit();
                return $this->success_message('تم تحديث حالة الجهاز بنجاح');
            } catch (Exception $e) {
                return $this->exception_message($e);
            }
        }
        $problems = ProblemCategory::all();
        $checks = CheckText::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        return view('dashboard.tech_invoices.update', compact('invoice', 'problems', 'checks', 'speed_devices', 'programe_devices'));
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

    public function ClientConnect(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $invoice->client_connect = $request->client_connect;
        $invoice->client_connect_notes = $request->client_connect_notes;
        $invoice->save();
        return $this->success_message(' تم تحديث حالة التواصل مع العميل بنجاح  ');
    }

}
