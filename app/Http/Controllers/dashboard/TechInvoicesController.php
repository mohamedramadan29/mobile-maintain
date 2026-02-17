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
use App\Models\dashboard\PieceSource;
use App\Models\dashboard\PriceDetail; ############# added ################
use App\Models\dashboard\SpeedDevice;
use App\Models\dashboard\InvoiceImage;
use App\Models\dashboard\InvoiceSteps;
use App\Models\dashboard\ProgrameDevice;
use Illuminate\Support\Facades\Redirect;
use App\Models\dashboard\ProblemCategory;
use App\Models\dashboard\InvoiceMoreCheck;
use App\Models\dashboard\InvoiceSpeedCheck;
use App\Models\dashboard\InvoicePrograneCheck;
use App\Models\dashboard\SpeedProblemCategory;
use App\Models\dashboard\ProgrameProblemCategory;

class TechInvoicesController extends Controller
{
    use Message_Trait;
    use Upload_Images;


    public function index()
    {
        $invoices = Invoice::where('admin_repair_id', Auth::guard('admin')->user()->id)->where('status', 'تحت الصيانة')->orderBy('id', 'desc')->paginate(10);
        return view('dashboard.tech_invoices.index', compact('invoices'));
    }

    public function search(Request $request)
    {
        $query = Invoice::where('admin_repair_id', Auth::guard('admin')->user()->id);

        // تحقق مما إذا كان هناك بحث عن حالة الفاتورة
        if ($request->has('invoice_status') && !empty($request->invoice_status)) {

            //  dd($request->invoice_status);
            // تحقق من القيم النصية "0" و "1"
            if ($request->invoice_status == 'تم تسليم الجهاز') {
                $query->where('delivery_status', 1);
            } elseif ($request->invoice_status == 'لم يتم التسليم') {
                $query->where('delivery_status', 0);
            } else {
                // في حالة وجود حالة غير الأرقام (مثل "رف الاستلام" أو "تحت الصيانة")
                $query->where('status', $request->invoice_status);
            }
        }
        $invoices = $query->orderBy('id', 'desc')->paginate(10)->appends($request->all());

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
        $invoice_more_checks = InvoiceMoreCheck::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        return view('dashboard.tech_invoices.show', compact(
            'invoice',
            'invoice_more_checks',
            'programe_problems',
            'speed_problems',
            'problems',
            'checks',
            'speed_devices',
            'programe_devices'
        ));
    }

    public function checkout(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if ($request->isMethod('post')) {
            $message_temp = Message::where('message_type', 'تحت الصيانة')->value('template_text');
            // dd($message_temp);
            ############# Check If This User Have More Invoice Or Not ##############
            try {
                // DB::beginTransaction();
                $invoices = Invoice::where('admin_repair_id', Auth::guard('admin')->user()->id)->where('status', 'تحت الصيانة')->count();
                $admin = Auth::guard('admin')->user();
                $available_number = $admin->device_nums;
                if ($invoices >= $available_number) {
                    DB::rollBack();
                    return view('dashboard.tech_invoices.checkout', compact('invoice'))  // نفس الـ view
                        ->with('error_message', 'لقد تجاوزت العدد المسموح به للعمل في نفس الوقت');
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
                    ['{name}', '{invoice_id}', '{status}', '{invoice_link}'],
                    [$invoice->name, $invoice->id, $invoice->status, $invoice_link],
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

                // DB::commit();
                // return $this->success_message('تم بدأ العمل علي الجهاز  بنجاح');
                return Redirect::route('dashboard.tech_invoices.index')->with(['تم بدأ العمل علي الجهاز  بنجاح']);
            } catch (Exception $e) {
                dd($e->getMessage());
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
        $old_invoice = Invoice::find($id);
        if ($request->isMethod('post')) {
            //dd($request->all());
            try {
                DB::beginTransaction();
                $status_changed = $request->status != $old_invoice->status;
                $invoice->status = $request->status;
                $invoice->tech_notes = $request->tech_notes;
                //$invoice->piece_resource = $request->piece_resource;
                $invoice->checkout_end_time = now();
                $invoice->save();
                ############ Start Price Details ################
                // حذف، تحديث، إضافة حسب البيانات
                // حذف كل تفاصيل السعر القديمة المرتبطة بالفاتورة
                $invoice->priceDetails()->delete();

                // ثم إضافة كل التفاصيل الجديدة
                if (!empty($request['price_details']) && is_array($request['price_details'])) {
                    foreach ($request['price_details'] as $detail) {
                        if (!empty($detail['amount'])) {
                            PriceDetail::create([
                                'invoice_id' => $invoice->id,
                                'title' => $detail['title'] ?? '', // قيمة فاضية إذا ما أرسل عنوان
                                'amount' => $detail['amount'],
                                'piece_resource' => $detail['piece_resource'] ?? null,
                            ]);
                        }
                    }
                }
                ############# Add Invoice Step ###############
                if ($status_changed) {
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
                        ['{name}', '{invoice_id}', '{status}', '{invoice_link}'],
                        [$invoice->name, $invoice->id, $invoice->status, $invoice_link],
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
                }

                ############### Add Files ####################

                if ($request->hasFile('file')) {
                    // dd($request->all());
                    $filename = $this->saveImage($request->file('file'), public_path('assets/uploads/invoices_files'));
                    $file = new InvoiceImage();
                    $file->invoice_id = $invoice->id;
                    $file->image = $filename;
                    $file->user_upload = Auth::id();
                    $file->title = $request->file_title;
                    $file->description = $request->file_description;
                    $file->price = $request->file_price ?? 0;
                    $file->file_type = 'file';
                    $file->save();
                }
                ################# Has Files For Images Status #############################
                if ($request->hasFile('file_status')) {
                    // dd($request->all());
                    $filename_status = $this->saveImage($request->file('file_status'), public_path('assets/uploads/invoices_files'));
                    $file = new InvoiceImage();
                    $file->invoice_id = $invoice->id;
                    $file->image = $filename_status;
                    $file->user_upload = Auth::id();
                    $file->title = $request->file_status_title;
                    $file->description = $request->file_status_description;
                    $file->file_type = 'status';
                    $file->save();
                }
                // return $this->success_message(' تم اضافة المرفق بنجاح  ');
                DB::commit();
                return $this->success_message('تم تحديث حالة الجهاز بنجاح');
            } catch (Exception $e) {
                dd($e);

                return $this->exception_message($e);
            }
        }
        $problems = ProblemCategory::all();
        $checks = CheckText::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        $invoice_more_checks = InvoiceMoreCheck::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $piece_resources = PieceSource::all();
        return view('dashboard.tech_invoices.update', compact('piece_resources', 'invoice', 'problems', 'checks', 'speed_devices', 'programe_devices', 'invoice_more_checks', 'programe_problems', 'speed_problems'));
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
            $file->price = $request->price ?? 0;
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
    public function showCompeleteInvoice($id)
    {
        $invoice = Invoice::find($id);
        $problems = ProblemCategory::all();
        $checks = CheckText::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        $invoice_more_checks = InvoiceMoreCheck::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $piece_resources = PieceSource::all();

        return view('dashboard.tech_invoices.compelete-invoice', compact('invoice', 'problems', 'checks', 'speed_devices', 'programe_devices', 'invoice_more_checks', 'programe_problems', 'speed_problems', 'piece_resources'));
    }

    public function ReturnToRoof($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $invoice->admin_repair_id = null;
        $invoice->status = 'رف الاستلام';
        $invoice->checkout_time = null;
        $invoice->checkout_end_time = null;
        $invoice->save();
        return $this->success_message('  تم ارجاع الجهاز الي رف الاستلام بنجاح  ');
    }
}
