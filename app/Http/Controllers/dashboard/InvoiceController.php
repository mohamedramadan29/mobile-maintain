<?php

namespace App\Http\Controllers\dashboard;

use Exception;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Jobs\SendCreateMessage;
use App\Models\dashboard\Admin;
use App\Models\dashboard\Invoice;
use App\Models\dashboard\Message;
use App\Http\Traits\Message_Trait;
use App\Http\Traits\Upload_Images;
use Illuminate\Support\Facades\DB;
use App\Models\dashboard\CheckText;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\dashboard\PieceSource;
use App\Models\dashboard\PriceDetail;
use App\Models\dashboard\SpeedDevice;
use Intervention\Image\Facades\Image;
use App\Models\dashboard\InvoiceCheck;
use App\Models\dashboard\InvoiceImage;
use App\Models\dashboard\InvoiceSteps;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\dashboard\ProgrameDevice;
use Illuminate\Support\Facades\Redirect;
use App\Models\dashboard\ProblemCategory;
// use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use App\Models\dashboard\InvoiceMoreCheck;
use App\Models\dashboard\InvoiceSpeedCheck;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\dashboard\InvoicePrograneCheck;
use App\Models\dashboard\SpeedProblemCategory;
use App\Models\dashboard\ProgrameProblemCategory;

class InvoiceController extends Controller
{
    use Message_Trait;
    use Upload_Images;
    public function index(Request $request)
    {
        $query = Invoice::query();
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

        $invoices = $query->orderBy('id', 'desc')->paginate(1000)->appends($request->all());
        $techs = Admin::where('type', 'فني')->get();

        return view('dashboard.invoices.index', compact('invoices', 'techs'));
    }


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

                return redirect()->route('dashboard.invoices.index')
                    ->with('Error_message', 'فشل إرسال الرسالة بسبب مشكلة في الاتصال،   تم تسليم الجهاز');
            }

            // تحليل استجابة الـ API
            $responseData = json_decode($response, true);
            if (isset($responseData['sent']) && $responseData['sent'] === true) {


                Log::info('WhatsApp message sent successfully and device delivered', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect()->route('dashboard.invoices.index')
                    ->with('Success_message', 'تم تسليم الجهاز وإرسال الرسالة بنجاح');
            } else {
                // تسجيل الخطأ في حالة فشل الإرسال
                $errorMessage = $responseData['message'] ?? 'سبب غير معروف';
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect()->route('dashboard.invoices.index')
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
                return redirect()->route('dashboard.invoices.index')
                    ->with('Error_message', 'فشل إرسال الرسالة بسبب مشكلة في الاتصال، لم يتم إرجاع الجهاز');
            }

            // تحليل استجابة الـ API
            $responseData = json_decode($response, true);
            if (isset($responseData['sent']) && $responseData['sent'] === true) {
                // الرسالة تم إرسالها بنجاح، قم بإرجاع الجهاز
                $invoice->delivery_status = 0;
                $invoice->save();
                Log::info('WhatsApp message sent successfully and device returned', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect()->route('dashboard.invoices.index')
                    ->with('Success_message', 'تم إرجاع الجهاز وإرسال الرسالة بنجاح');
            } else {
                // تسجيل الخطأ في حالة فشل الإرسال
                $errorMessage = $responseData['message'] ?? 'سبب غير معروف';
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect()->route('dashboard.invoices.index')
                    ->with('Error_message', 'فشل إرسال الرسالة: ' . $errorMessage . '، لم يتم إرجاع الجهاز');
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
                $signuturepath = public_path('assets/uploads/invoices_files/' . $filesiguture);

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
                        $filename = $this->saveImage($image, public_path('assets/uploads/invoices_files'));
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
                            $path = public_path('assets/uploads/invoices_files/') . $fileName;

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
                SendCreateMessage::dispatch($invoice, $message);

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
        $checks = CheckText::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        $piece_resources = PieceSource::all();
        $invoice_more_checks = InvoiceMoreCheck::all();
        return view('dashboard.invoices.create', compact(
            'problems',
            'checks',
            'speed_devices',
            'programe_devices',
            'programe_problems',
            'speed_problems',
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
                        $filename = $this->saveImage($image, public_path('assets/uploads/invoices_files'));
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
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        return view('dashboard.invoices.update', compact(
            'invoice',
            'problems',
            'checks',
            'speed_devices',
            'programe_devices',
            'programe_problems',
            'speed_problems',
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
                    @unlink(public_path('assets/uploads/invoices_files/' . $file->image));
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
            @unlink(public_path('assets/uploads/invoices_files/' . $file->image));
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
        $invoice_more_checks = InvoiceMoreCheck::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        $piece_resources = PieceSource::all();
        return view('dashboard.invoices.show-details', compact('piece_resources', 'invoice', 'problems', 'checks', 'speed_devices', 'programe_devices', 'invoice_more_checks', 'programe_problems', 'speed_problems'));
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
                return redirect()->route('dashboard.invoices.index')
                    ->with('Error_message', 'فشل إرسال الرسالة بسبب مشكلة في الاتصال، لم يتم تسليم الجهاز');
            }

            // تحليل استجابة الـ API
            $responseData = json_decode($response, true);
            if (isset($responseData['sent']) && $responseData['sent'] === true) {
                // الرسالة تم إرسالها بنجاح، قم بتسليم الجهاز
                $invoice->delivery_status = 1;
                $invoice->save();

                Log::info('WhatsApp message sent successfully and device delivered', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect()->route('dashboard.invoices.index')
                    ->with('Success_message', 'تم تسليم الجهاز وإرسال الرسالة بنجاح');
            } else {
                // تسجيل الخطأ في حالة فشل الإرسال
                $errorMessage = $responseData['message'] ?? 'سبب غير معروف';
                Log::error('Failed to send WhatsApp message', [
                    'phone' => $new_phone,
                    'response' => $responseData,
                ]);
                return redirect()->route('dashboard.invoices.index')
                    ->with('Error_message', 'فشل إرسال الرسالة: ' . $errorMessage . '، لم يتم تسليم الجهاز');
            }
        }

        // عرض صفحة تسليم الفاتورة
        return view('dashboard.invoices.delivery_with_details', compact('invoice', 'programe_problems', 'speed_problems', 'piece_resources', 'problems', 'checks', 'speed_devices', 'programe_devices', 'invoice_more_checks'));
    }
}
