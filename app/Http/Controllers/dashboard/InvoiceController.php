<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\Message_Trait;
use App\Http\Traits\Upload_Images;
use App\Jobs\SendCreateMessage;
use App\Models\dashboard\Admin;
use App\Models\dashboard\CheckText;
use App\Models\dashboard\Invoice;
use App\Models\dashboard\InvoiceCheck;
use App\Models\dashboard\InvoiceImage;
use App\Models\dashboard\InvoiceMoreCheck;
use App\Models\dashboard\InvoicePcCheck;
use App\Models\dashboard\InvoicePrograneCheck;
use App\Models\dashboard\InvoiceSonyCheck;
use App\Models\dashboard\InvoiceSpeedCheck;
use App\Models\dashboard\InvoiceSteps;
use App\Models\dashboard\Message;
use App\Models\dashboard\PcDevice;
use App\Models\dashboard\PcProblemCategory;
use App\Models\dashboard\PieceSource;
use App\Models\dashboard\PriceDetail;
use App\Models\dashboard\ProblemCategory;
use App\Models\dashboard\ProgrameDevice;
use App\Models\dashboard\ProgrameProblemCategory;
use App\Models\dashboard\SonyDevice;
use App\Models\dashboard\SonyProblemCategory;
use App\Models\dashboard\SpeedDevice;
use App\Models\dashboard\SpeedProblemCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Mpdf\Mpdf;
use Picqer\Barcode\BarcodeGeneratorPNG;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceController extends Controller
{
    use Message_Trait;
    use Upload_Images;
    public function index(Request $request)
    {
        // eager loading يمنع تنفيذ استعلامين لكل صف عند عرض اسم الاستقبال والفني
        $query = Invoice::query()->with(['Recieved:id,name', 'Technical:id,name']);

        // البحث العام (رقم الفاتورة، الاسم، الهاتف)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Date range filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        // Exclude archived invoices for better performance, unless searching specifically
        if (!$request->has('search') || empty($request->search)) {
            $query->whereDoesntHave('archives', function ($q) {
                $q->where('status', 'archived');
            });
        }

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

        // Calculate statistics based on the same date range filters
        $statsQuery = Invoice::query();
        if ($request->has('from_date') && !empty($request->from_date)) {
            $statsQuery->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $statsQuery->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        $total_delivered = (clone $statsQuery)->where('delivery_status', 1)->count();
        $total_undelivered = (clone $statsQuery)->where('delivery_status', 0)->count();

        $invoices = $query->orderBy('id', 'desc')->paginate(50)->appends($request->all());
        $techs = Admin::where('type', 'فني')->get();

        return view('dashboard.invoices.index', compact('invoices', 'techs', 'total_delivered', 'total_undelivered'));
    }


    ########################################################################################
    public function deviceDeliverd(Request $request)
    {
        // eager loading يمنع تنفيذ استعلامين لكل صف عند عرض اسم الاستقبال والفني
        $query = Invoice::query()->with(['Recieved:id,name', 'Technical:id,name']);

        // البحث العام (رقم الفاتورة، الاسم، الهاتف)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Date range filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        // تحقق مما إذا كان هناك بحث عن حالة الفاتورة
        if ($request->has('invoice_status') && !empty($request->invoice_status)) {
            if ($request->invoice_status == 'تم تسليم الجهاز') {
                $query->where('delivery_status', 1);
            } elseif ($request->invoice_status == 'لم يتم التسليم') {
                $query->where('delivery_status', 0);
            } else {
                $query->where('status', $request->invoice_status);
            }
        }

        // Calculate statistics based on the same date range filters
        $statsQuery = Invoice::query();
        if ($request->has('from_date') && !empty($request->from_date)) {
            $statsQuery->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $statsQuery->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        $total_delivered = (clone $statsQuery)->where('delivery_status', 1)->count();
        $total_undelivered = (clone $statsQuery)->where('delivery_status', 0)->count();

        $invoices = $query->orderBy('id', 'desc')->where('delivery_status', 1)->paginate(50)->appends($request->all());
        $techs = Admin::where('type', 'فني')->get();

        return view('dashboard.invoices.device_deliverd', compact('invoices', 'techs', 'total_delivered', 'total_undelivered'));
    }

    public function deviceUnDeliverd(Request $request)
    {
        // eager loading يمنع تنفيذ استعلامين لكل صف عند عرض اسم الاستقبال والفني
        $query = Invoice::query()->with(['Recieved:id,name', 'Technical:id,name']);

        // البحث العام (رقم الفاتورة، الاسم، الهاتف)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        }

        // Date range filter
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        // تحقق مما إذا كان هناك بحث عن حالة الفاتورة
        if ($request->has('invoice_status') && !empty($request->invoice_status)) {
            if ($request->invoice_status == 'تم تسليم الجهاز') {
                $query->where('delivery_status', 1);
            } elseif ($request->invoice_status == 'لم يتم التسليم') {
                $query->where('delivery_status', 0);
            } else {
                $query->where('status', $request->invoice_status);
            }
        }

        // Calculate statistics based on the same date range filters
        $statsQuery = Invoice::query();
        if ($request->has('from_date') && !empty($request->from_date)) {
            $statsQuery->where('created_at', '>=', $request->from_date . ' 00:00:00');
        }
        if ($request->has('to_date') && !empty($request->to_date)) {
            $statsQuery->where('created_at', '<=', $request->to_date . ' 23:59:59');
        }

        $total_delivered = (clone $statsQuery)->where('delivery_status', 1)->count();
        $total_undelivered = (clone $statsQuery)->where('delivery_status', 0)->count();

        $invoices = $query->orderBy('id', 'desc')->where('delivery_status', 0)->paginate(50)->appends($request->all());
        $techs = Admin::where('type', 'فني')->get();

        return view('dashboard.invoices.device_undeliverd', compact('invoices', 'techs', 'total_delivered', 'total_undelivered'));
    }

    ########################################################################################

    public function bulkDeleteConfirm(Request $request)
    {
        // الحصول على معرفات الفواتير من معلمات الاستعلام
        $invoiceIds = explode(',', $request->query('invoice_ids', ''));

        // التحقق من معرفات الفواتير
        if (empty($invoiceIds) || !is_array($invoiceIds)) {
            return redirect()->back()->with('error', 'لم يتم اختيار أي فواتير.');
        }

        // جلب الفواتير
        $invoices = Invoice::whereIn('id', $invoiceIds)->get();

        return view('dashboard.invoices.bulk_delete_confirm', compact('invoices', 'invoiceIds'));
    }

    public function bulkDelete(Request $request)
    {
        // التحقق من الإدخال
        $request->validate([
            'invoice_ids' => 'required',
        ]);

        // الحصول على معرفات الفواتير
        $invoiceIds = explode(',', $request->input('invoice_ids'));

        // حذف الفواتير
        Invoice::whereIn('id', $invoiceIds)->delete();

        return redirect()->route('dashboard.invoices.index')->with('Success_message', 'تم حذف الفواتير المختارة بنجاح.');
    }

    public function SendMessageRecieve(Request $request, $id)
    {


        $invoice = Invoice::findOrFail($id);

        if ($request->isMethod('post')) {
            // إعداد الرسالة المرسلة للعميل
            $message_temp = Message::where('message_type', 'اضافة فاتورة')->value('template_text');
            $new_phone = preg_replace('/^0/', '', $invoice->phone);
            $new_phone = '966' . $new_phone; // إضافة رمز البلد +966
            ########### Send Message To WhatsApp
            // إنشاء رابط عام للفاتورة
            $invoice_link = url('dashboard/invoice/view/' . $invoice->id);
            ########### Dynamic Message
            // استبدال المتغيرات بالقيم الفعلية
            $message = str_replace(
                ['{name}', '{invoice_id}', '{phone}', '{date_delivery}', '{time_delivery}', '{description}', '{invoice_link}'],
                [$invoice->name, $invoice->id, $invoice->phone, $invoice->date_delivery, $invoice->time_delivery, $invoice->description ?? "لا توجد ملاحظات", $invoice_link],
                $message_temp
            );

            // إعداد الطلب لإرسال الرسالة عبر API
            $params = array(
                'instanceid' => '138796',
                'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
                'phone' => $new_phone,
                'body' => $message,
            );
            $queryString = http_build_query($params);

            // إرسال الرسالة باستخدام cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.4whats.net/sendMessage/?" . $queryString,
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

            // التحقق من نتيجة الإرسال
            if ($err) {
                // تسجيل الخطأ في حالة وجود مشكلة في الاتصال
                Log::error('Failed to send WhatsApp message due to connection error', [
                    'phone' => $new_phone,
                    'error' => $err,
                ]);
                return redirect()->route('dashboard.invoices.index')
                    ->with('Error_message', 'فشل إرسال الرسالة بسبب مشكلة في الاتصال، لم يتم إرسال رسالة تسجيل الفاتورة');
            }

            // تحليل استجابة الـ API
            $responseData = json_decode($response, true);
            if (isset($responseData['sent']) && $responseData['sent'] === true) {
                // الرسالة تم إرسالها بنجاح، قم بتسليم الجهاز
                $invoice->message_send = 1;
                $invoice->save();

                Log::info('WhatsApp message sent successfully and device delivered', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect()->route('dashboard.invoices.index')
                    ->with('Success_message', 'تم إرسال رسالة تسجيل الفاتورة بنجاح');
            } else {
                // تسجيل الخطأ في حالة فشل الإرسال
                $errorMessage = $responseData['message'] ?? 'سبب غير معروف';
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect()->route('dashboard.invoices.index')
                    ->with('Error_message', 'فشل إرسال الرسالة: ' . $errorMessage . '، لم يتم إرسال رسالة تسجيل الفاتورة');
            }
        }
        // عرض صفحة إرسال الرسالة
        return view('dashboard.invoices.send-message-recieve', compact('invoice'));
    }

    public function delivery(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($request->isMethod('post')) {
            // الرسالة تم إرسالها بنجاح، قم بتسليم الجهاز
            $invoice->delivery_status = 1;
            $invoice->actual_date_delivery = date('Y-m-d');
            $invoice->actual_time_delivery = date('H:i:s');
            $invoice->save();
            // إعداد الرسالة المرسلة للعميل
            $message_temp = Message::where('message_type', 'تسليم الجهاز')->value('template_text');
            $new_phone = preg_replace('/^0/', '', $invoice->phone);
            $new_phone = '966' . $new_phone; // إضافة رمز البلد +966
            $message = str_replace(['{name}'], [$invoice->name], $message_temp);

            // إعداد الطلب لإرسال الرسالة عبر API
            $params = array(
                'instanceid' => '138796',
                'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
                'phone' => $new_phone,
                'body' => $message,
            );
            $queryString = http_build_query($params);

            // إرسال الرسالة باستخدام cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.4whats.net/sendMessage/?" . $queryString,
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

            // التحقق من نتيجة الإرسال
            if ($err) {
                // تسجيل الخطأ في حالة وجود مشكلة في الاتصال
                Log::error('Failed to send WhatsApp message due to connection error', [
                    'phone' => $new_phone,
                    'error' => $err,
                ]);

                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Error_message', 'فشل إرسال الرسالة بسبب مشكلة في الاتصال،   تم تسليم الجهاز');
            }

            // تحليل استجابة الـ API
            $responseData = json_decode($response, true);
            if (isset($responseData['sent']) && $responseData['sent'] === true) {


                Log::info('WhatsApp message sent successfully and device delivered', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Success_message', 'تم تسليم الجهاز وإرسال الرسالة بنجاح');
            } else {
                // تسجيل الخطأ في حالة فشل الإرسال
                $errorMessage = $responseData['message'] ?? 'سبب غير معروف';
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Error_message', 'فشل إرسال الرسالة: ' . $errorMessage . '،   تم تسليم الجهاز');
            }
        }

        // عرض صفحة تسليم الفاتورة
        return view('dashboard.invoices.delivery_status', compact('invoice'));
    }

    public function undelivery(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            // البحث عن الفاتورة
            $invoice = Invoice::findOrFail($id);

            // الرسالة تم إرسالها بنجاح، قم بإرجاع الجهاز
            $invoice->delivery_status = 0;
            $invoice->status = 'تحت الصيانة';
            $invoice->actual_date_delivery = null;
            $invoice->actual_time_delivery = null;
            $invoice->save();



            // إعداد الرسالة المرسلة للعميل
            $message_temp = Message::where('message_type', 'عودة الجهاز')->value('template_text');
            $new_phone = preg_replace('/^0/', '', $invoice->phone);
            $new_phone = '966' . $new_phone; // إضافة رمز البلد +966
            $message = str_replace(['{name}'], [$invoice->name], $message_temp);

            // إعداد الطلب لإرسال الرسالة عبر API
            $params = array(
                'instanceid' => '138796',
                'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
                'phone' => $new_phone,
                'body' => $message,
            );
            $queryString = http_build_query($params);

            // إرسال الرسالة باستخدام cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.4whats.net/sendMessage/?" . $queryString,
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

            // التحقق من نتيجة الإرسال
            if ($err) {
                // تسجيل الخطأ في حالة وجود مشكلة في الاتصال
                Log::error('Failed to send WhatsApp message due to connection error', [
                    'phone' => $new_phone,
                    'error' => $err,
                ]);
                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Error_message', 'فشل إرسال الرسالة:    تم إرجاع الجهاز');
            }

            // تحليل استجابة الـ API
            $responseData = json_decode($response, true);
            if (isset($responseData['sent']) && $responseData['sent'] === true) {

                Log::info('WhatsApp message sent successfully and device returned', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Success_message', 'تم إرجاع الجهاز وإرسال الرسالة بنجاح');
            } else {
                // تسجيل الخطأ في حالة فشل الإرسال
                $errorMessage = $responseData['message'] ?? 'سبب غير معروف';
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Error_message', 'فشل إرسال الرسالة: ' . $errorMessage . '،   تم إرجاع الجهاز');
            }
        }

        // عرض صفحة إرجاع الفاتورة
        $invoice = Invoice::findOrFail($id);
        return view('dashboard.invoices.undelivery_status', compact('invoice'));
    }

    public function create(Request $request)
    {

        ///$message_temp = Message::where('message_type', 'اضافة فاتورة')->select('template_text')->first();
        $message_temp = Message::where('message_type', 'اضافة فاتورة')->value('template_text');
        // dd($message_temp);
        if ($request->isMethod('post')) {
            try {
                //dd($request->all());
                $data = $request->all();

                ///  dd($data);
                $rules = [
                    'name' => 'required',
                    'phone' => 'required',
                    'title' => 'required',
                    'problems' => 'required',
                    'description' => 'nullable',
                    'price' => 'required',
                    'date_delivery' => 'required',
                    'time_delivery' => 'required',
                    'status' => 'required',
                    'signature' => 'required', // إضافة التوقيع
                    'checkout_type' => 'required',
                    'work.*' => 'required|in:0,1'
                ];
                $messages = [
                    'name.required' => 'من فضلك ادخل اسم العميل ',
                    'phone.required' => 'من فضلك ادخل رقم الهاتف ',
                    'title.required' => 'من فضلك ادخل عنوان الفاتورة ',
                    'problems.required' => 'من فضلك ادخل المشاكل ',
                    // 'description.required' => 'من فضلك ادخل الوصف ',
                    'price.required' => 'من فضلك ادخل السعر ',
                    'date_delivery.required' => 'من فضلك ادخل تاريخ الاستلام ',
                    'time_delivery.required' => 'من فضلك ادخل وقت الاستلام ',
                    'status.required' => 'من فضلك ادخل حالة الفاتورة ',
                    'signature.required' => 'يرجى توقيع الفاتورة قبل الحفظ.',
                    'checkout_type.required' => 'من فضلك ادخل نوع الفحص ',
                ];
                $validator = Validator::make($data, $rules, $messages);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                // حفظ التوقيع مباشرة
                // إزالة رأس الـ Data URL
                $base64Image = preg_replace('/^data:.+;base64,/', '', $request->signature);

                // فك تشفير البيانات
                $imageData = base64_decode($base64Image);

                // إنشاء اسم الملف بشكل فريد
                $filesiguture = 'signature_' . time() . '.png';

                // مسار حفظ الصورة
                $signuturepath = 'assets/uploads/invoices_files/' . $filesiguture;

                // حفظ الصورة في المسار المحدد
                file_put_contents($signuturepath, $imageData);
                // تحويل المصفوفة إلى JSON قبل التخزين
                $patternJson = json_encode($data['pattern']);

                // DB::beginTransaction();
                $invoice = new Invoice();
                $invoice->invoice_number = 1;
                $invoice->name = $data['name'];
                $invoice->phone = $data['phone'];
                $invoice->title = $data['title'];
                $invoice->problems = json_encode($data['problems']);
                $invoice->description = $data['description'];
                $invoice->price = $data['price'];
                $invoice->date_delivery = $data['date_delivery'];
                $invoice->time_delivery = $data['time_delivery'];
                $invoice->status = $data['status'];
                $invoice->admin_recieved_id = Auth::id();
                $invoice->signature = $filesiguture;
                $invoice->device_password_text = $data['device_text_password'];
                $invoice->device_pattern = $patternJson;
                $invoice->checkout_type = $data['checkout_type'];
                $invoice->message_send = 0;
                // $invoice->piece_resource = $data['piece_resource'];
                $more_checks = $data['invoice_more_checks'] ?? null;
                $invoice->invoice_more_checks = $more_checks ? json_encode($data['invoice_more_checks']) : null;
                $invoice->save();
                ############ Start Insert Files ################
                if ($request->hasFile('files_images')) {
                    // $files = $request->file('files');
                    foreach ($request->file('files_images') as $image) {
                        $filename = $this->saveImage($image, 'assets/uploads/invoices_files');
                        $invoice_image = new InvoiceImage();
                        $invoice_image->invoice_id = $invoice->id;
                        $invoice_image->image = $filename;
                        $invoice_image->user_upload = Auth::id();
                        $invoice_image->save();
                    }
                }
                // حفظ الصور الملتقطة من الكاميرا
                if (!empty($data['captured_images']) && is_array($data['captured_images'])) {
                    foreach ($data['captured_images'] as $base64Image) {
                        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
                            $imageData = base64_decode($imageData);
                            $extension = strtolower($type[1]);

                            // إنشاء اسم فريد للصورة
                            $fileName = uniqid() . '.' . $extension;
                            $path ='assets/uploads/invoices_files/' . $fileName;

                            // حفظ الصورة في المجلد
                            file_put_contents($path, $imageData);

                            // تخزينها في قاعدة البيانات
                            $invoice_image = new InvoiceImage();
                            $invoice_image->invoice_id = $invoice->id;
                            $invoice_image->image = $fileName;
                            $invoice_image->user_upload = Auth::id();
                            $invoice_image->save();
                        }
                    }
                }
                ############# Add Invoice Step ###############
                $invoice_step = new InvoiceSteps();
                $invoice_step->invoice_id = $invoice->id;
                $invoice_step->admin_id = Auth::id();
                $invoice_step->step_details = ' تم اضافة الفاتورة  ';
                $invoice_step->save();

                if ($invoice->checkout_type == 'فحص كامل') {
                    // إضافة نتائج الفحص
                    if (isset($data['problem_id']) && is_array($data['problem_id'])) {
                        foreach ($data['problem_id'] as $index => $problemId) {
                            $check = new InvoiceCheck();
                            $check->invoice_id = $invoice->id;
                            $check->problem_id = $problemId;
                            $check->problem_name = $data['check_problem_name'][$index] ?? '';
                            $check->work = $data["work_{$problemId}"] ?? 0;
                            $check->notes = $data['notes'][$index] ?? null;
                            $check->after_check = $data['after_check'][$index] ?? null;
                            $check->save();
                        }
                    }
                }
                if ($invoice->checkout_type == 'فحص جهاز سريع') {
                    // إضافة نتائج الفحص للجهاز السريع
                    if (isset($data['speed_id']) && is_array($data['speed_id'])) {
                        foreach ($data['speed_id'] as $index => $speedId) {
                            $checkSpeed = new InvoiceSpeedCheck();
                            $checkSpeed->invoice_id = $invoice->id;
                            $checkSpeed->speed_id = $speedId;
                            $checkSpeed->problem_name = $data['check_speed_name'][$index] ?? '';

                            $checkSpeed->work = $data["speedwork_{$speedId}"] ?? 0;
                            $checkSpeed->notes = $data['speed_notes'][$index] ?? null;
                            $checkSpeed->after_check = $data['after_check_speed'][$index] ?? null;
                            $checkSpeed->save();
                        }
                    }
                }
                if ($invoice->checkout_type == 'فحص جهاز برمجة') {
                    // إضافة نتائج الفحص للجهاز البرمجة
                    if (isset($data['programe_id']) && is_array($data['programe_id'])) {
                        foreach ($data['programe_id'] as $index => $programeId) {
                            $checkPrograme = new InvoicePrograneCheck();
                            $checkPrograme->invoice_id = $invoice->id;
                            $checkPrograme->programe_id = $programeId;
                            $checkPrograme->problem_name = $data['check_programe_name'][$index] ?? '';
                            $checkPrograme->work = $data["programework_{$programeId}"] ?? 0;
                            $checkPrograme->notes = $data['programe_notes'][$index] ?? null;
                            $checkPrograme->after_check = $data['after_check_programe'][$index] ?? null;
                            $checkPrograme->save();
                        }
                    }
                }
                if ($invoice->checkout_type == 'فحص جهاز سوني') {
                    // إضافة نتائج الفحص للجهاز السريع
                    if (isset($data['sony_id']) && is_array($data['sony_id'])) {
                        foreach ($data['sony_id'] as $index => $sonyId) {
                            $checkSony = new InvoiceSonyCheck();
                            $checkSony->invoice_id = $invoice->id;
                            $checkSony->sony_id = $sonyId;
                            $checkSony->problem_name = $data['check_sony_name'][$index] ?? '';

                            $checkSony->work = $data["sonywork_{$sonyId}"] ?? 0;
                            $checkSony->notes = $data['sony_notes'][$index] ?? null;
                            $checkSony->after_check = $data['after_check_sony'][$index] ?? null;
                            $checkSony->save();
                        }
                    }
                }
                   if ($invoice->checkout_type == 'فحص جهاز pc') {
                    // إضافة نتائج الفحص للجهاز السريع
                    if (isset($data['pc_id']) && is_array($data['pc_id'])) {
                        foreach ($data['pc_id'] as $index => $pcId) {
                            $checkPc = new InvoicePcCheck();
                            $checkPc->invoice_id = $invoice->id;
                            $checkPc->pc_id = $pcId;
                            $checkPc->problem_name = $data['check_pc_name'][$index] ?? '';

                            $checkPc->work = $data["pcwork_{$pcId}"] ?? 0;
                            $checkPc->notes = $data['pc_notes'][$index] ?? null;
                            $checkPc->after_check = $data['after_check_pc'][$index] ?? null;
                            $checkPc->save();
                        }
                    }
                }
                ########## ADD Price Details ##################
                // حفظ تفاصيل السعر إن وجدت
                if (!empty($data['price_details']) && is_array($data['price_details'])) {
                    foreach ($data['price_details'] as $detail) {
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
                ########### Send Message To WhatsApp

                // إنشاء رابط عام للفاتورة
                $invoice_link = url('dashboard/invoice/view/' . $invoice->id);
                ########### Dynamic Message
                // استبدال المتغيرات بالقيم الفعلية
                $message = str_replace(
                    ['{name}', '{invoice_id}', '{phone}', '{date_delivery}', '{time_delivery}', '{description}', '{invoice_link}'],
                    [$invoice->name, $invoice->id, $invoice->phone, $invoice->date_delivery, $invoice->time_delivery, $invoice->description ?? "لا توجد ملاحظات", $invoice_link],
                    $message_temp
                );
                ######
                ########
                #########
                // SendCreateMessage::dispatch($invoice, $message);

                // DB::commit();
                // إعادة توجيه مع رسالة نجاح وبيانات إضافية
                return redirect()->route('dashboard.invoices.create')->with('Success_message', 'تم إضافة الفاتورة بنجاح')->with('new_invoice_id', $invoice->id);
                //return Redirect::route('dashboard.invoices.print_barcode', $invoice->id);
            } catch (Exception $e) {
                dd($e);
                return Redirect()->back()->withInput()->withErrors($e->getMessage());
                //return $this->exception_message($e);
            }
        }
        $problems = ProblemCategory::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $sony_problems = SonyProblemCategory::all();
        $pc_problems = PcProblemCategory::all();
        $checks = CheckText::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        $sony_devices = SonyDevice::all();
        $pc_devices = PcDevice::all();
        $piece_resources = PieceSource::all();
        $invoice_more_checks = InvoiceMoreCheck::all();
        return view('dashboard.invoices.create', compact(
            'problems',
            'checks',
            'speed_devices',
            'programe_devices',
            'sony_devices',
            'pc_devices',
            'programe_problems',
            'speed_problems',
            'pc_problems',
            'sony_problems',
            'piece_resources',
            'invoice_more_checks'
        ));
    }



    public function SendMessage($id)
    {
        $invoice = Invoice::find($id);
        $message_temp = Message::where('message_type', 'اضافة فاتورة')->value('template_text');
        ########### Send Message To WhatsApp
        // إنشاء رابط عام للفاتورة
        $invoice_link = url('dashboard/invoice/view/' . $invoice->id);
        ########### Dynamic Message
        // استبدال المتغيرات بالقيم الفعلية
        $message = str_replace(
            ['{name}', '{invoice_id}', '{phone}', '{date_delivery}', '{time_delivery}', '{description}', '{invoice_link}'],
            [$invoice->name, $invoice->id, $invoice->phone, $invoice->date_delivery, $invoice->time_delivery, $invoice->description ?? "لا توجد ملاحظات", $invoice_link],
            $message_temp
        );
        SendCreateMessage::dispatch($invoice, $message);
        return redirect()->route('dashboard.invoices.index')->with('Success_message', 'تم اعادة ارسال الرسالة بنجاح');
    }
    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $piece_resources = PieceSource::all();
        $invoice_more_checks = InvoiceMoreCheck::all();
        if ($request->isMethod('post')) {
            try {
                $data = $request->all();
                // dd($data);
                $rules = [
                    'name' => 'required',
                    'phone' => 'required',
                    'title' => 'required',
                    'problems' => 'required',
                    // 'description' => 'required',
                    'price' => 'required',
                    'date_delivery' => 'required',
                    'time_delivery' => 'required',
                    'status' => 'required',
                    'checkout_type' => 'required',
                ];
                $messages = [
                    'name.required' => 'من فضلك ادخل اسم العميل ',
                    'phone.required' => 'من فضلك ادخل رقم الهاتف ',
                    'title.required' => 'من فضلك ادخل عنوان الفاتورة ',
                    'problems.required' => 'من فضلك ادخل المشاكل ',
                    // 'description.required' => 'من فضلك ادخل الوصف ',
                    'price.required' => 'من فضلك ادخل السعر ',
                    'date_delivery.required' => 'من فضلك ادخل تاريخ الاستلام ',
                    'time_delivery.required' => 'من فضلك ادخل وقت الاستلام ',
                    'status.required' => 'من فضلك ادخل حالة الفاتورة ',
                    'checkout_type.required' => 'من فضلك ادخل نوع الفحص ',
                ];
                $validator = Validator::make($data, $rules, $messages);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                DB::beginTransaction();
                // تحويل المصفوفة إلى JSON قبل التخزين
                $patternJson = json_encode($data['pattern']);
                $invoice->name = $data['name'];
                $invoice->phone = $data['phone'];
                $invoice->title = $data['title'];
                $invoice->problems = json_encode($data['problems']);
                $invoice->description = $data['description'];
                $invoice->price = $data['price'];
                $invoice->date_delivery = $data['date_delivery'];
                $invoice->time_delivery = $data['time_delivery'];
                $invoice->status = $data['status'];
                $invoice->device_password_text = $data['device_text_password'];
                $invoice->device_pattern = $patternJson;
                // $invoice->piece_resource = $data['piece_resource'];
                $more_checks = $data['invoice_more_checks'] ?? null;
                $invoice->invoice_more_checks = $more_checks ? json_encode($data['invoice_more_checks']) : null;
                $invoice->save();
                ############ Start Price Details ################
                // حذف، تحديث، إضافة حسب البيانات
                // حذف كل تفاصيل السعر القديمة المرتبطة بالفاتورة
                $invoice->priceDetails()->delete();
                // ثم إضافة كل التفاصيل الجديدة
                if (!empty($data['price_details']) && is_array($data['price_details'])) {
                    foreach ($data['price_details'] as $detail) {
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

                ############ End Price Details ################
                ############ Start Insert Files ################
                if ($request->hasFile('files_images')) {
                    // $files = $request->file('files');
                    foreach ($request->file('files_images') as $image) {
                        $filename = $this->saveImage($image, 'assets/uploads/invoices_files');
                        $invoice_image = new InvoiceImage();
                        $invoice_image->invoice_id = $invoice->id;
                        $invoice_image->image = $filename;
                        $invoice_image->user_upload = Auth::id();
                        $invoice_image->save();
                    }
                }

                ############# Add Invoice Step ###############
                $invoice_step = new InvoiceSteps();
                $invoice_step->invoice_id = $invoice->id;
                $invoice_step->admin_id = Auth::id();
                $invoice_step->step_details = ' تم تعديل الفاتورة  ';
                $invoice_step->save();
                if ($invoice->checkout_type == 'فحص كامل') {
                    // إضافة الفحوصات أو تعديلها
                    if (isset($data['problem_id']) && is_array($data['problem_id'])) {
                        foreach ($data['problem_id'] as $index => $problem_id) {
                            InvoiceCheck::updateOrCreate(
                                [
                                    'invoice_id' => $invoice->id,
                                    'problem_id' => $problem_id,
                                ],
                                [
                                    'work' => $data['work_' . $problem_id] ?? null, // تأكد من أن القيمة موجودة
                                    'notes' => $data['notes'][$index] ?? '', // استخدام الترتيب الصحيح للمصفوفة
                                    'after_check' => $data['after_check'][$index] ?? '', // استخدام الترتيب الصحيح للمصفوفة
                                ]
                            );
                        }
                    }
                }
                if ($invoice->checkout_type == 'فحص جهاز سريع') {
                    // إضافة نتائج الفحص للجهاز السريع
                    if (isset($data['speed_id']) && is_array($data['speed_id'])) {
                        foreach ($data['speed_id'] as $index => $speedId) {
                            InvoiceSpeedCheck::updateOrCreate(
                                [
                                    "invoice_id" => $invoice->id,
                                    "speed_id" => $speedId,
                                ],
                                [
                                    // "problem_name" => $data['check_speed_name'][$index] ?? '',
                                    "work" => $data['speedwork_' . $speedId] ?? null,
                                    "notes" => $data['speed_notes'][$index] ?? '',
                                    "after_check" => $data['after_check_speed'][$index] ?? '',
                                ]
                            );
                        }
                    }
                }
                if ($invoice->checkout_type == 'فحص جهاز برمجة') {
                    // إضافة نتائج الفحص للجهاز البرمجة
                    if (isset($data['programe_id']) && is_array($data['programe_id'])) {
                        foreach ($data['programe_id'] as $index => $programeId) {
                            $checkPrograme = InvoicePrograneCheck::updateOrCreate(
                                [
                                    "invoice_id" => $invoice->id,
                                    "programe_id" => $programeId,
                                ],
                                [
                                    "problem_name" => $data['check_programe_name'][$index] ?? '',
                                    "work" => isset($data["programework_{$programeId}"]) ? reset($data["programework_{$programeId}"]) : 0,
                                    "notes" => $data['programe_notes'][$index] ?? null,
                                    "after_check" => $data['after_check_programe'][$index] ?? null,
                                ]
                            );
                        }
                    }
                }
                if ($invoice->checkout_type == 'فحص جهاز سوني') {
                    // إضافة نتائج الفحص للجهاز السوني
                    if (isset($data['sony_id']) && is_array($data['sony_id'])) {
                        foreach ($data['sony_id'] as $index => $sonyId) {
                            $checkSony = InvoiceSonyCheck::updateOrCreate(
                                [
                                    "invoice_id" => $invoice->id,
                                    "sony_id" => $sonyId,
                                ],
                                [
                                    "problem_name" => $data['check_sony_name'][$index] ?? '',
                                    "work" => isset($data["sonywork_{$sonyId}"]) ? (is_array($data["sonywork_{$sonyId}"]) ? reset($data["sonywork_{$sonyId}"]) : $data["sonywork_{$sonyId}"]) : 0,
                                    "notes" => $data['sony_notes'][$index] ?? null,
                                    "after_check" => $data['after_check_sony'][$index] ?? null,
                                ]
                            );
                        }
                    }
                }
                if ($invoice->checkout_type == 'فحص جهاز pc') {
                    // إضافة نتائج الفحص لجهاز pc
                    if (isset($data['pc_id']) && is_array($data['pc_id'])) {
                        foreach ($data['pc_id'] as $index => $pcId) {
                            $checkPc = InvoicePcCheck::updateOrCreate(
                                [
                                    "invoice_id" => $invoice->id,
                                    "pc_id" => $pcId,
                                ],
                                [
                                    "problem_name" => $data['check_pc_name'][$index] ?? '',
                                    "work" => isset($data["pcwork_{$pcId}"]) ? (is_array($data["pcwork_{$pcId}"]) ? reset($data["pcwork_{$pcId}"]) : $data["pcwork_{$pcId}"]) : 0,
                                    "notes" => $data['pc_notes'][$index] ?? null,
                                    "after_check" => $data['after_check_pc'][$index] ?? null,
                                ]
                            );
                        }
                    }
                }
                DB::commit();
                return $this->success_message(' تم تعديل الفاتورة بنجاح');
            } catch (Exception $e) {
                return $this->exception_message($e);
            }
        }
        $checks = CheckText::all();
        $problems = ProblemCategory::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $sony_problems = SonyProblemCategory::all();
        $pc_problems = PcProblemCategory::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        $sony_devices = SonyDevice::all();
        $pc_devices = PcDevice::all();
        return view('dashboard.invoices.update', compact(
            'invoice',
            'problems',
            'checks',
            'speed_devices',
            'programe_devices',
            'sony_devices',
            'pc_devices',
            'programe_problems',
            'speed_problems',
            'sony_problems',
            'pc_problems',
            'piece_resources',
            'invoice_more_checks'
        ));
    }
    public function destroy(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            try {
                $invoice = Invoice::find($id);
                ////////// Delete Files
                $files = InvoiceImage::where('invoice_id', $id)->get();
                foreach ($files as $file) {
                    @unlink('assets/uploads/invoices_files/' . $file->image);
                    $file->delete();
                }
                $invoice->delete();
                return redirect()->route('dashboard.invoices.index')->with('Success_message', ' تم حذف الفاتورة بنجاح');
            } catch (Exception $e) {
                return $this->exception_message($e);
            }
        }
        $invoice = Invoice::find($id);
        return view('dashboard.invoices.delete', compact('invoice'));
    }

    public function delete_file($id)
    {
        try {
            $file = InvoiceImage::find($id);
            @unlink('assets/uploads/invoices_files/' . $file->image);
            $file->delete();
            return $this->success_message(' تم حذف الملف بنجاح');
        } catch (Exception $e) {
            return $this->exception_message($e);
        }
    }

    public function print($id)
    {
        $invoice = Invoice::find($id);
        return view('dashboard.invoices.print', compact('invoice'));
    }

    public function steps($id)
    {
        $steps = InvoiceSteps::where('invoice_id', $id)->get();
        $invoice = Invoice::find($id);
        return view('dashboard.invoices.steps', compact('steps', 'invoice'));
    }

    public function add_tech(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            DB::beginTransaction();
            $invoice = Invoice::find($id);
            $invoice->admin_repair_id = $request->admin_repair_id;
            $invoice->status = 'تحت الصيانة';
            $invoice->checkout_time = now();
            $invoice->save();
            ############# Add Invoice Step ###############
            $invoice_step = new InvoiceSteps();
            $invoice_step->invoice_id = $invoice->id;
            $invoice_step->admin_id = Auth::id();
            $invoice_step->step_details = ' تم تعين فني من جانب المدير  ';
            $invoice_step->save();
            DB::commit();
            return redirect()->route('dashboard.invoices.index')->with('Success_message', 'تم تعين فني من جانب المدير  بنجاح');
        }
        $techs = Admin::where('type', 'فني')->get();
        $invoice = Invoice::find($id);
        return view('dashboard.invoices.add_tech_invoice', compact('techs', 'invoice'));
    }

    ################# Start Print BarCode ################
    public function print_barcode($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            $piece_resources = PieceSource::all();
            // توليد رابط الفاتورة
            $invoiceUrl = url('dashboard/invoice/view/' . $invoice->id);
            // توليد QR Code وتحويله إلى Base64
            // $qrCode = QrCode::format('png')->size(200)->generate($invoiceUrl);
            $qrCode = QrCode::format('png')
                ->size(100) // تقليل الحجم ليلائم العرض
                ->margin(0) // إزالة الهوامش
                ->generate($invoiceUrl);
            $qrCodeBase64 = base64_encode($qrCode);
            $qrCodeBase64 = base64_encode($qrCode);

            // إعداد PDF باستخدام Mpdf
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'default_font' => 'Zain',
                'format' => [50, 26],
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);

            // إرسال البيانات إلى View
            $html = view('dashboard.invoices.barcode_pdf', compact('invoice', 'qrCodeBase64', 'piece_resources'))->render();

            // كتابة الـ HTML في PDF
            $mpdf->WriteHTML($html);

            // عرض الـ PDF مباشرة أو تحميله
            return $mpdf->Output("Invoice_{$invoice->id}.pdf", 'I');
        } catch (Exception $e) {
            return back()->withErrors('حدث خطأ أثناء الطباعة: ' . $e->getMessage());
        }
    }
    ################### End Print BarCode ################
    ################ Priview Barcode ##################

    public function preview_barcode($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoiceUrl = url('dashboard/invoice/view/' . $invoice->id);
        $qrCode = QrCode::format('png')->size(100)->margin(0)->generate($invoiceUrl);
        $qrCodeBase64 = base64_encode($qrCode);

        return view('dashboard.invoices.barcode_preview', compact('invoice', 'qrCodeBase64'));
    }
    ################### End Preview Barcode ####################
    ################### Start Invoice Haif time ###############

    public function InvoicesHaifTime()
    {
        //$invoices = Invoice::orderBy('id', 'desc')->paginate(10);
        $invoices = Invoice::where('status', 'رف الاستلام')->get()->filter(function ($invoice) {
            $deliveryDateTime = Carbon::parse($invoice->date_delivery . ' ' . $invoice->time_delivery);
            $createdDateTime = Carbon::parse($invoice->created_at);
            // حساب نصف الوقت بدقة أكبر
            $halfTime = $createdDateTime->copy()->addMinutes($createdDateTime->diffInMinutes($deliveryDateTime) / 2);
            // الشرط الأول: تجاوز نصف الوقت
            $halfTimePassed = Carbon::now()->greaterThanOrEqualTo($halfTime);
            // الشرط الثاني: الوقت انتهى بالفعل
            $deliveryTimePassed = Carbon::now()->greaterThanOrEqualTo($deliveryDateTime);
            return $halfTimePassed || $deliveryTimePassed;
        });
        $techs = Admin::where('type', 'فني')->get();
        return view('dashboard.invoices.invoice_haif_time', compact('invoices', 'techs'));
    }
    ################## End  Invoices Haif Time

    ######################## Show Invoice All Details ############

    public function show_details($id)
    {
        $invoice = Invoice::find($id);
        $problems = ProblemCategory::all();
        $checks = CheckText::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        $sony_devices = SonyDevice::all();
        $pc_devices = PcDevice::all();
        $invoice_more_checks = InvoiceMoreCheck::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $sony_problems = SonyProblemCategory::all();
        $pc_problems = PcProblemCategory::all();
        $piece_resources = PieceSource::all();
        return view('dashboard.invoices.show-details', compact('piece_resources', 'invoice', 'problems', 'checks', 'speed_devices', 'programe_devices', 'sony_devices', 'pc_devices', 'invoice_more_checks', 'programe_problems', 'speed_problems', 'sony_problems', 'pc_problems'));
    }
    ###################### End Invoice All Details ###############
    ########################## Return To Rouf ########################
    #############################
    ###################################

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


    public function ReturnToUndeliveryStatus($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $invoice->delivery_status = 0;
        $invoice->save();
        return $this->success_message(' تم ارجاع الجهاز لحالة لم يتم التسليم  ');
    }

     public function ReturnToTech($invoice_id){
        $invoice = Invoice::find($invoice_id);
        $invoice->delivery_status = 0;
        $invoice->status = 'تحت الصيانة';
        $invoice->actual_date_delivery = null;
        $invoice->actual_time_delivery = null;
        $invoice->save();
        return $this->success_message(' تم ارجاع الجهاز الي الفني  ');
    }

    ################### ################### Start Delivery Status WithDetails ###################
    #################

    public function deliveryWithDetails(Request $request, $id)
    {

        $invoice = Invoice::findOrFail($id);

        $problems = ProblemCategory::all();
        $checks = CheckText::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        $invoice_more_checks = InvoiceMoreCheck::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $piece_resources = PieceSource::all();


        if ($request->isMethod('post')) {
            // الرسالة تم إرسالها بنجاح، قم بتسليم الجهاز
            $invoice->delivery_status = 1;
            $invoice->actual_date_delivery = date('Y-m-d');
            $invoice->actual_time_delivery = date('H:i:s');
            $invoice->save();
            // إعداد الرسالة المرسلة للعميل
            $message_temp = Message::where('message_type', 'تسليم الجهاز')->value('template_text');
            $new_phone = preg_replace('/^0/', '', $invoice->phone);
            $new_phone = '966' . $new_phone; // إضافة رمز البلد +966
            $message = str_replace(['{name}'], [$invoice->name], $message_temp);

            // إعداد الطلب لإرسال الرسالة عبر API
            $params = array(
                'instanceid' => '138796',
                'token' => '3fc4ad69-3ea3-4307-923c-7080f7aa0d8e',
                'phone' => $new_phone,
                'body' => $message,
            );
            $queryString = http_build_query($params);

            // إرسال الرسالة باستخدام cURL
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.4whats.net/sendMessage/?" . $queryString,
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

            // التحقق من نتيجة الإرسال
            if ($err) {
                // تسجيل الخطأ في حالة وجود مشكلة في الاتصال
                Log::error('Failed to send WhatsApp message due to connection error', [
                    'phone' => $new_phone,
                    'error' => $err,
                ]);
                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Error_message', 'فشل إرسال الرسالة بسبب مشكلة في الاتصال، لم يتم تسليم الجهاز');
            }

            // تحليل استجابة الـ API
            $responseData = json_decode($response, true);
            if (isset($responseData['sent']) && $responseData['sent'] === true) {


                Log::info('WhatsApp message sent successfully and device delivered', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Success_message', 'تم تسليم الجهاز وإرسال الرسالة بنجاح');
            } else {
                // تسجيل الخطأ في حالة فشل الإرسال
                $errorMessage = $responseData['message'] ?? 'سبب غير معروف';
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect($request->redirect_to ?? route('dashboard.invoices.index'))
                    ->with('Error_message', 'فشل إرسال الرسالة: ' . $errorMessage . '، لم يتم تسليم الجهاز');
            }
        }

        // عرض صفحة تسليم الفاتورة
        return view('dashboard.invoices.delivery_with_details', compact('invoice', 'programe_problems', 'speed_problems', 'piece_resources', 'problems', 'checks', 'speed_devices', 'programe_devices', 'invoice_more_checks'));
    }

    ######################## تقرير أوقات الذروة ########################

    /**
     * تقرير أوقات الذروة: يوضح أكثر ساعات اليوم وأيام الأسبوع ازدحامًا
     * بإدخال الفواتير (أي استلام الأجهزة)، لمساعدة الإدارة على توزيع الموظفين.
     */
    public function peakTimesReport(Request $request)
    {
        $from = $request->input('from_date');
        $to   = $request->input('to_date');

        $base = function () use ($from, $to) {
            $q = Invoice::query();
            // مقارنة مباشرة على العمود حتى يُستخدم فهرس created_at
            if (!empty($from)) {
                $q->where('created_at', '>=', $from . ' 00:00:00');
            }
            if (!empty($to)) {
                $q->where('created_at', '<=', $to . ' 23:59:59');
            }
            return $q;
        };

        // استعلام واحد يغطي الساعات وأيام الأسبوع والخريطة الحرارية معًا (168 صفًا كحد أقصى)
        $grid = $base()
            ->selectRaw('DAYOFWEEK(created_at) as day_num, HOUR(created_at) as hour_num, COUNT(*) as total')
            ->groupBy('day_num', 'hour_num')
            ->get();

        $hourly = array_fill(0, 24, 0);
        $weekly = array_fill(1, 7, 0);
        $heatmap = [];
        for ($d = 1; $d <= 7; $d++) {
            $heatmap[$d] = array_fill(0, 24, 0);
        }

        foreach ($grid as $row) {
            $d = (int) $row->day_num;
            $h = (int) $row->hour_num;
            $c = (int) $row->total;
            $hourly[$h] += $c;
            $weekly[$d] += $c;
            $heatmap[$d][$h] = $c;
        }

        $total = array_sum($hourly);

        // تقسيم اليوم إلى 6 فترات من 4 ساعات (يُحسب من المصفوفة أعلاه بلا استعلام إضافي)
        $block_names = [
            0  => 'من ١٢ منتصف الليل إلى ٤ فجرًا',
            4  => 'من ٤ فجرًا إلى ٨ صباحًا',
            8  => 'من ٨ صباحًا إلى ١٢ ظهرًا',
            12 => 'من ١٢ ظهرًا إلى ٤ عصرًا',
            16 => 'من ٤ عصرًا إلى ٨ مساءً',
            20 => 'من ٨ مساءً إلى ١٢ منتصف الليل',
        ];
        $block_short = [
            0 => '١٢ ص – ٤ ص', 4 => '٤ ص – ٨ ص', 8 => '٨ ص – ١٢ ظ',
            12 => '١٢ ظ – ٤ م', 16 => '٤ م – ٨ م', 20 => '٨ م – ١٢ ص',
        ];

        $blocks = [];
        foreach (array_keys($block_names) as $start) {
            $count = 0;
            for ($i = $start; $i < $start + 4; $i++) {
                $count += $hourly[$i];
            }
            $blocks[$start] = [
                'start' => $start,
                'name'  => $block_names[$start],
                'short' => $block_short[$start],
                'count' => $count,
                'pct'   => $total ? round($count / $total * 100, 1) : 0,
            ];
        }
        $peak_block = $total ? collect($blocks)->sortByDesc('count')->first() : null;

        // نفس التقسيم لكل يوم من أيام الأسبوع
        $blocks_by_day = [];
        for ($d = 1; $d <= 7; $d++) {
            foreach (array_keys($block_names) as $start) {
                $c = 0;
                for ($i = $start; $i < $start + 4; $i++) {
                    $c += $heatmap[$d][$i];
                }
                $blocks_by_day[$d][$start] = $c;
            }
        }

        // أعلى ساعة وأعلى يوم
        $peak_hour = $total ? array_search(max($hourly), $hourly) : null;
        $peak_hour_count = $total ? max($hourly) : 0;
        $peak_day = $total ? array_search(max($weekly), $weekly) : null;
        $peak_day_count = $total ? max($weekly) : 0;

        // أزحم ثلاثة أيام فعلية + متوسط اليوم الواحد
        $busiest_dates = $base()
            ->selectRaw('DATE(created_at) as the_date, COUNT(*) as total')
            ->groupBy('the_date')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $working_days = $base()->selectRaw('COUNT(DISTINCT DATE(created_at)) as d')->value('d');
        $avg_per_day = $working_days ? round($total / $working_days, 1) : 0;

        // أفضل نافذة متصلة من 3 ساعات (مفيدة لتحديد وقت تكثيف الموظفين)
        $best_window = ['start' => null, 'count' => 0];
        if ($total) {
            for ($h = 0; $h < 24; $h++) {
                $sum = $hourly[$h] + $hourly[($h + 1) % 24] + $hourly[($h + 2) % 24];
                if ($sum > $best_window['count']) {
                    $best_window = ['start' => $h, 'count' => $sum];
                }
            }
        }

        // تسميات الساعات بنظام 12 ساعة (ص/م) لأن نظام 24 ساعة ملتبس على المستخدم
        $hour_labels = [];
        for ($h = 0; $h < 24; $h++) {
            $suffix = $h < 12 ? 'ص' : 'م';
            $h12 = $h % 12;
            if ($h12 === 0) {
                $h12 = 12;
            }
            $hour_labels[$h] = [
                'short' => $h12 . ' ' . $suffix,                       // ٩ م
                'long'  => $h12 . ' ' . ($h < 12 ? 'صباحًا' : 'مساءً'), // ٩ مساءً
                'h24'   => sprintf('%02d:00', $h),                      // 21:00
            ];
        }
        // منتصف الليل والظهر: نوضحهما صراحة لإزالة أي لبس
        $hour_labels[0]['long']  = '١٢ منتصف الليل';
        $hour_labels[0]['short'] = '12 ص';
        $hour_labels[12]['long'] = '١٢ ظهرًا';

        // ترقيم MySQL لـ DAYOFWEEK يبدأ من الأحد = 1
        $days_ar = [
            1 => 'الأحد',
            2 => 'الاثنين',
            3 => 'الثلاثاء',
            4 => 'الأربعاء',
            5 => 'الخميس',
            6 => 'الجمعة',
            7 => 'السبت',
        ];

        return view('dashboard.invoices.peak_times', compact(
            'days_ar',
            'hour_labels',
            'blocks',
            'peak_block',
            'blocks_by_day',
            'block_short',
            'hourly',
            'weekly',
            'heatmap',
            'total',
            'peak_hour',
            'peak_hour_count',
            'peak_day',
            'peak_day_count',
            'busiest_dates',
            'avg_per_day',
            'working_days',
            'best_window',
            'from',
            'to'
        ));
    }

    public function bulkFilter(Request $request)
    {
        // التحقق من الإدخال
        $request->validate([
            'invoice_ids' => 'required',
        ]);

        // الحصول على معرفات الفواتير
        $invoiceIds = explode(',', $request->input('invoice_ids'));

        // تحديث الفواتير المحددة
        Invoice::whereIn('id', $invoiceIds)->update([
            'status' => 'تم الاصلاح',
            'delivery_status' => 1,
            // الإبقاء على الفني الحالي لكل فاتورة، وتعيين 17 فقط لمن ليس له فني
            'admin_repair_id' => DB::raw('COALESCE(admin_repair_id, 17)'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تصفية الفواتير المختارة بنجاح'
        ]);
    }
    /**
     * الحد الأقصى لعدد الأجهزة في الدفعة الواحدة.
     * سبب التحديد: post_max_size = 20M و max_file_uploads = 20 على السيرفر،
     * لذلك تُرفع المرفقات بطلب منفصل لكل جهاز ويبقى حجم الدفعة معقولاً.
     */
    public const BULK_DELIVERY_MAX = 15;

    /**
     * تحويل مدخل المعرفات (نص مفصول بفواصل أو مصفوفة) إلى مصفوفة أرقام فريدة.
     */
    private function parseInvoiceIds($input): array
    {
        $ids = is_array($input) ? $input : explode(',', (string) $input);

        return collect($ids)
            ->map(fn($id) => (int) trim((string) $id))
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * الحد الأقصى لحجم المرفق الواحد بالكيلوبايت، حسب إعدادات PHP على السيرفر.
     */
    private function maxUploadKb(): int
    {
        $toKb = function ($value) {
            $value = trim((string) $value);
            $unit = strtolower(substr($value, -1));
            $number = (float) $value;

            return (int) match ($unit) {
                'g' => $number * 1024 * 1024,
                'm' => $number * 1024,
                'k' => $number,
                default => $number / 1024,
            };
        };

        $limits = array_filter([$toKb(ini_get('upload_max_filesize')), $toKb(ini_get('post_max_size'))]);

        return $limits ? (int) min($limits) : 20480;
    }

    /**
     * فحص الأجهزة المحددة قبل التسليم الجماعي.
     * يُرجع الأجهزة المكتملة (جاهزة للتسليم مباشرة) والأجهزة الناقصة
     * (تحتاج صورة/فيديو أو تحديد حالة الإصلاح) لعرضها في نافذة التحديث السريع.
     */
    public function bulkDeliveryCheck(Request $request)
    {
        $request->validate(['invoice_ids' => 'required']);

        $ids = $this->parseInvoiceIds($request->input('invoice_ids'));

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم اختيار أي أجهزة',
            ]);
        }

        if (count($ids) > self::BULK_DELIVERY_MAX) {
            return response()->json([
                'success' => false,
                'message' => 'الحد الأقصى ' . self::BULK_DELIVERY_MAX . ' جهاز في الدفعة الواحدة، وقد حددت ' . count($ids) . ' جهاز. من فضلك قسّمها على أكثر من دفعة.',
            ]);
        }

        $invoices = Invoice::with('statusFiles:id,invoice_id')
            ->whereIn('id', $ids)
            ->where('delivery_status', 0)
            ->get();

        $ready = [];
        $incomplete = [];

        foreach ($invoices as $invoice) {
            $hasStatus = $invoice->hasRepairStatus();
            $hasMedia = $invoice->statusFiles->isNotEmpty();

            if ($hasStatus && $hasMedia) {
                $ready[] = $invoice->id;
                continue;
            }

            $incomplete[] = [
                'id' => $invoice->id,
                'device' => $invoice->title,
                'name' => $invoice->name,
                'phone' => $invoice->phone,
                'status' => $hasStatus ? $invoice->status : null,
                'has_status' => $hasStatus,
                'has_media' => $hasMedia,
            ];
        }

        return response()->json([
            'success' => true,
            'ready' => $ready,
            'incomplete' => $incomplete,
            'unavailable' => array_values(array_diff($ids, $invoices->pluck('id')->all())),
            'repair_statuses' => Invoice::REPAIR_STATUSES,
            'max_upload_kb' => $this->maxUploadKb(),
        ]);
    }

    /**
     * التحديث السريع لجهاز واحد من نافذة التسليم الجماعي:
     * حفظ حالة الإصلاح ورفع صورة/فيديو الحالة.
     * المرفق يُرفع بطلب منفصل لكل جهاز احتراماً لحدود post_max_size على السيرفر.
     */
    public function bulkDeliveryQuickUpdate(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|integer|exists:invoices,id',
            'status' => ['nullable', 'string', Rule::in(Invoice::REPAIR_STATUSES)],
            'file' => 'nullable|file|mimes:jpg,jpeg,png,webp,heic,heif,mp4,mov,m4v,3gp|max:' . $this->maxUploadKb(),
        ], [
            'file.max' => 'حجم المرفق أكبر من الحد المسموح (' . round($this->maxUploadKb() / 1024) . ' ميجابايت).',
            'file.mimes' => 'نوع المرفق غير مدعوم، المسموح: صور (jpg, png, heic) أو فيديو (mp4, mov).',
        ]);

        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($invoice->delivery_status == 1) {
            return response()->json([
                'success' => false,
                'message' => 'الجهاز رقم ' . $invoice->id . ' مسلَّم بالفعل',
            ]);
        }

        try {
            DB::beginTransaction();

            if ($request->filled('status') && $request->status !== $invoice->status) {
                $invoice->status = $request->status;
                $invoice->save();

                $invoice_step = new InvoiceSteps();
                $invoice_step->invoice_id = $invoice->id;
                $invoice_step->admin_id = Auth::id();
                $invoice_step->step_details = 'تحديث سريع لحالة الجهاز إلى (' . $request->status . ') من شاشة التسليم الجماعي';
                $invoice_step->save();
            }

            if ($request->hasFile('file')) {
                $filename = $this->saveImage($request->file('file'), public_path('assets/uploads/invoices_files'));

                $file = new InvoiceImage();
                $file->invoice_id = $invoice->id;
                $file->image = $filename;
                $file->user_upload = Auth::id();
                $file->title = 'صورة حالة الجهاز عند التسليم';
                $file->file_type = 'status';
                $file->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Bulk delivery quick update failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'تعذّر حفظ بيانات الجهاز رقم ' . $invoice->id,
            ], 500);
        }

        $invoice->refresh()->load('statusFiles:id,invoice_id');

        return response()->json([
            'success' => true,
            'invoice_id' => $invoice->id,
            'has_status' => $invoice->hasRepairStatus(),
            'has_media' => $invoice->statusFiles->isNotEmpty(),
        ]);
    }

    /**
     * التسليم الجماعي للأجهزة المحددة.
     * تحقق مرن: حالة الإصلاح شرط أساسي، أما الصورة فاختيارية
     * بشرط وصول إشارة تأكيد صريحة من الموظف (confirm_without_image).
     */
    public function bulkDelivery(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required',
            'confirm_without_image' => 'nullable',
        ]);

        $ids = $this->parseInvoiceIds($request->input('invoice_ids'));
        $confirmedWithoutImage = $this->parseInvoiceIds($request->input('confirm_without_image', []));

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم اختيار أي أجهزة',
            ]);
        }

        if (count($ids) > self::BULK_DELIVERY_MAX) {
            return response()->json([
                'success' => false,
                'message' => 'الحد الأقصى ' . self::BULK_DELIVERY_MAX . ' جهاز في الدفعة الواحدة',
            ]);
        }

        $invoices = Invoice::with('statusFiles:id,invoice_id')
            ->whereIn('id', $ids)
            ->where('delivery_status', 0)
            ->get();

        if ($invoices->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم العثور على أجهزة صالحة للتسليم',
            ]);
        }

        $delivered = [];
        $rejected = [];

        // إعداد الرسالة المرسلة للعميل
        $message_temp = Message::where('message_type', 'تسليم الجهاز')->value('template_text');

        foreach ($invoices as $invoice) {
            if (!$invoice->hasRepairStatus()) {
                $rejected[] = [
                    'id' => $invoice->id,
                    'reason' => 'لم تُحدَّد حالة الإصلاح (تم / لم يتم)',
                ];
                continue;
            }

            $hasMedia = $invoice->statusFiles->isNotEmpty();

            if (!$hasMedia && !in_array($invoice->id, $confirmedWithoutImage, true)) {
                $rejected[] = [
                    'id' => $invoice->id,
                    'reason' => 'لا توجد صورة/فيديو ولم يتم تأكيد التسليم بدونها',
                ];
                continue;
            }

            try {
                DB::beginTransaction();

                $invoice->delivery_status = 1;
                $invoice->actual_date_delivery = date('Y-m-d');
                $invoice->actual_time_delivery = date('H:i:s');
                $invoice->delivered_at = now();
                $invoice->delivered_by = Auth::id();
                $invoice->delivery_without_image = $hasMedia ? 0 : 1;
                $invoice->save();

                $invoice_step = new InvoiceSteps();
                $invoice_step->invoice_id = $invoice->id;
                $invoice_step->admin_id = Auth::id();
                $invoice_step->step_details = $hasMedia
                    ? 'تم التسليم ضمن دفعة تسليم جماعي'
                    : 'تم التسليم ضمن دفعة تسليم جماعي بدون إرفاق صورة بتأكيد الموظف';
                $invoice_step->save();

                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Bulk delivery failed for invoice', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);

                $rejected[] = [
                    'id' => $invoice->id,
                    'reason' => 'خطأ أثناء الحفظ، لم يتم التسليم',
                ];
                continue;
            }

            $delivered[] = $invoice->id;

            // إرسال رسالة الواتساب إذا وجد قالب
            if ($message_temp) {
                $message = str_replace(['{name}'], [$invoice->name], $message_temp);
                SendCreateMessage::dispatch($invoice, $message);
            }
        }

        if (empty($delivered)) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم تسليم أي جهاز',
                'delivered' => [],
                'rejected' => $rejected,
            ]);
        }

        $message = 'تم تسليم ' . count($delivered) . ' جهاز بنجاح';

        if (!empty($rejected)) {
            $message .= ' — وتم تجاهل ' . count($rejected) . ' جهاز';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'delivered' => $delivered,
            'rejected' => $rejected,
        ]);
    }
}
