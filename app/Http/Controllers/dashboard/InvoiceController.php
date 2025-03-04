<?php

namespace App\Http\Controllers\dashboard;

use App\Models\dashboard\InvoicePrograneCheck;
use App\Models\dashboard\InvoiceSpeedCheck;
use Exception;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\dashboard\Admin;
use App\Models\dashboard\Invoice;
use App\Models\dashboard\Message;
use App\Http\Traits\Message_Trait;
use App\Http\Traits\Upload_Images;
use Illuminate\Support\Facades\DB;
use App\Models\dashboard\CheckText;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\dashboard\SpeedDevice;
use Intervention\Image\Facades\Image;
use App\Models\dashboard\InvoiceCheck;
use App\Models\dashboard\InvoiceImage;
use App\Models\dashboard\InvoiceSteps;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\dashboard\ProblemCategory;
use App\Models\dashboard\ProgrameDevice;
// use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceController extends Controller
{
    use Message_Trait;
    use Upload_Images;
    public function index(Request $request)
    {
        $query = Invoice::query();

        // تحقق مما إذا كان هناك بحث عن حالة الفاتورة
        if ($request->has('invoice_status') && !empty($request->invoice_status)) {
            $query->where('status', $request->invoice_status);
        }

        $invoices = $query->orderBy('id', 'desc')->paginate(10);
        $techs = Admin::where('type', 'فني')->get();

        return view('dashboard.invoices.index', compact('invoices', 'techs'));
    }
    public function create(Request $request)
    {
        ///$message_temp = Message::where('message_type', 'اضافة فاتورة')->select('template_text')->first();
        $message_temp = Message::where('message_type', 'اضافة فاتورة')->value('template_text');
        // dd($message_temp);
        if ($request->isMethod('post')) {
            try {
                $data = $request->all();

                ///  dd($data);
                $rules = [
                    'name' => 'required',
                    'phone' => 'required',
                    'title' => 'required',
                    'problems' => 'required',
                    'description' => 'required',
                    'price' => 'required',
                    'date_delivery' => 'required',
                    'time_delivery' => 'required',
                    'status' => 'required',
                    'signature' => 'required', // إضافة التوقيع
                ];
                $messages = [
                    'name.required' => 'من فضلك ادخل اسم العميل ',
                    'phone.required' => 'من فضلك ادخل رقم الهاتف ',
                    'title.required' => 'من فضلك ادخل عنوان الفاتورة ',
                    'problems.required' => 'من فضلك ادخل المشاكل ',
                    'description.required' => 'من فضلك ادخل الوصف ',
                    'price.required' => 'من فضلك ادخل السعر ',
                    'date_delivery.required' => 'من فضلك ادخل تاريخ الاستلام ',
                    'time_delivery.required' => 'من فضلك ادخل وقت الاستلام ',
                    'status.required' => 'من فضلك ادخل حالة الفاتورة ',
                    'signature.required' => 'يرجى توقيع الفاتورة قبل الحفظ.',
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

                DB::beginTransaction();
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
                ############# Add Invoice Step ###############
                $invoice_step = new InvoiceSteps();
                $invoice_step->invoice_id = $invoice->id;
                $invoice_step->admin_id = Auth::id();
                $invoice_step->step_details = ' تم اضافة الفاتورة  ';
                $invoice_step->save();

                // إضافة نتائج الفحص
                if (isset($data['problem_id']) && is_array($data['problem_id'])) {
                    foreach ($data['problem_id'] as $index => $problemId) {
                        $check = new InvoiceCheck();
                        $check->invoice_id = $invoice->id;
                        $check->problem_id = $problemId;
                        $check->problem_name = $data['check_problem_name'][$index] ?? '';
                        $check->work = isset($data["work_{$problemId}"]) ? reset($data["work_{$problemId}"]) : 0;
                        $check->notes = $data['notes'][$index] ?? null;
                        $check->after_check = $data['after_check'][$index] ?? null;
                        $check->save();
                    }
                }
                // إضافة نتائج الفحص للجهاز السريع
                if (isset($data['speed_id']) && is_array($data['speed_id'])) {
                    foreach ($data['speed_id'] as $index => $speedId) {
                        $checkSpeed = new InvoiceSpeedCheck();
                        $checkSpeed->invoice_id = $invoice->id;
                        $checkSpeed->speed_id = $speedId;
                        $checkSpeed->problem_name = $data['check_speed_name'][$index] ?? '';
                        $checkSpeed->work = isset($data["speedwork_{$speedId}"]) ? reset($data["speedwork_{$speedId}"]) : 0;
                        $checkSpeed->notes = $data['speed_notes'][$index] ?? null;
                        $checkSpeed->after_check = $data['after_check_speed'][$index] ?? null;
                        $checkSpeed->save();
                    }
                }
                // إضافة نتائج الفحص للجهاز البرمجة
                if (isset($data['programe_id']) && is_array($data['programe_id'])) {
                    foreach ($data['programe_id'] as $index => $programeId) {
                        $checkPrograme = new InvoicePrograneCheck();
                        $checkPrograme->invoice_id = $invoice->id;
                        $checkPrograme->programe_id = $programeId;
                        $checkPrograme->problem_name = $data['check_programe_name'][$index] ?? '';
                        $checkPrograme->work = isset($data["programework_{$programeId}"]) ? reset($data["programework_{$programeId}"]) : 0;
                        $checkPrograme->notes = $data['programe_notes'][$index] ?? null;
                        $checkPrograme->after_check = $data['after_check_programe'][$index] ?? null;
                        $checkPrograme->save();
                    }
                }
                ########### Send Message To WhatsApp
                // إنشاء رابط عام للفاتورة

                $invoice_link = url('dashboard/invoice/view/' . $invoice->id);
                $new_phone = preg_replace('/^0/', '', $invoice->phone);
                // إضافة رمز البلد +966
                $new_phone = '966' . $new_phone;
                // $new_phone = '201002292856';


                //$new_phone = $invoice->phone;
                // تنسيق رسالة واتساب بطريقة مميزة
                // $message = "📄 *تفاصيل فاتورتك* 📄\n\n";
                // $message .= "👤 *العميل:* " . $invoice->name . "\n";
                // $message .= "📞 *رقم الهاتف:* " . $invoice->phone . "\n";
                // $message .= "📅 *تاريخ التسليم:* " . $invoice->date_delivery . "\n";
                // $message .= "⏰ *وقت التسليم:* " . $invoice->time_delivery . "\n";
                // //$message .= "💰 *السعر:* " . number_format($invoice->price, 2) . " ريال\n";
                // //$message .= "📌 *حالة الفاتورة:* " . $invoice->status . "\n\n";
                // $message .= "🖋 *الملاحظات:* " . ($invoice->description ?? "لا توجد ملاحظات") . "\n\n";
                // $message .= "🔗 *رابط الفاتورة:* " . $invoice_link . "\n";
                ########### Dynamic Message
                // استبدال المتغيرات بالقيم الفعلية

                $message = str_replace(
                    ['{name}', '{phone}', '{date_delivery}', '{time_delivery}', '{description}', '{invoice_link}'],
                    [$invoice->name, $invoice->phone, $invoice->date_delivery, $invoice->time_delivery, $invoice->description ?? "لا توجد ملاحظات", $invoice_link],
                    $message_temp
                );
                // dd($message);

                // تعريف المتغير
                $params = array(
                    'instanceid' => '138484',
                    'token' => '573f5335-db32-422f-8a7f-efc7a18654f9',
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
                DB::commit();
                return $this->success_message(' تم اضافة الفاتورة بنجاح');
            } catch (Exception $e) {
                return Redirect()->back()->withInput()->withErrors($e->getMessage());
                //return $this->exception_message($e);
            }
        }

        $problems = ProblemCategory::all();
        $checks = CheckText::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        return view('dashboard.invoices.create', compact('problems', 'checks', 'speed_devices', 'programe_devices'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if ($request->isMethod('post')) {
            try {
                $data = $request->all();
                // dd($data);
                $rules = [
                    'name' => 'required',
                    'phone' => 'required',
                    'title' => 'required',
                    'problems' => 'required',
                    'description' => 'required',
                    'price' => 'required',
                    'date_delivery' => 'required',
                    'time_delivery' => 'required',
                    'status' => 'required',

                ];
                $messages = [
                    'name.required' => 'من فضلك ادخل اسم العميل ',
                    'phone.required' => 'من فضلك ادخل رقم الهاتف ',
                    'title.required' => 'من فضلك ادخل عنوان الفاتورة ',
                    'problems.required' => 'من فضلك ادخل المشاكل ',
                    'description.required' => 'من فضلك ادخل الوصف ',
                    'price.required' => 'من فضلك ادخل السعر ',
                    'date_delivery.required' => 'من فضلك ادخل تاريخ الاستلام ',
                    'time_delivery.required' => 'من فضلك ادخل وقت الاستلام ',
                    'status.required' => 'من فضلك ادخل حالة الفاتورة ',
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
                $invoice->save();
                ############ Start Insert Files ################
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
                DB::commit();
                return $this->success_message(' تم تعديل الفاتورة بنجاح');
            } catch (Exception $e) {
                return $this->exception_message($e);
            }
        }
        $checks = CheckText::all();
        $problems = ProblemCategory::all();
        $speed_devices = SpeedDevice::all();
        $programe_devices = ProgrameDevice::all();
        return view('dashboard.invoices.update', compact('invoice', 'problems', 'checks', 'speed_devices', 'programe_devices'));
    }
    public function destroy($id)
    {
        try {
            $invoice = Invoice::find($id);
            ////////// Delete Files
            $files = InvoiceImage::where('invoice_id', $id)->get();
            foreach ($files as $file) {
                @unlink(public_path('assets/uploads/invoices_files/' . $file->image));
                $file->delete();
            }
            $invoice->delete();
            return $this->success_message(' تم حذف الفاتورة بنجاح');
        } catch (Exception $e) {
            return $this->exception_message($e);
        }
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

    public function add_tech(Request $reques, $id)
    {
        DB::beginTransaction();
        $invoice = Invoice::find($id);
        $invoice->admin_repair_id = $reques->admin_repair_id;
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
        return $this->success_message('تم تعين فني من جانب المدير  بنجاح');
    }

    ################# Start Print BarCode ################
    public function print_barcode($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
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
                'format' => [50, 25],
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);

            // إرسال البيانات إلى View
            $html = view('dashboard.invoices.barcode_pdf', compact('invoice', 'qrCodeBase64'))->render();

            // كتابة الـ HTML في PDF
            $mpdf->WriteHTML($html);

            // عرض الـ PDF مباشرة أو تحميله
            return $mpdf->Output("Invoice_{$invoice->id}.pdf", 'I');

        } catch (Exception $e) {
            return back()->withErrors('حدث خطأ أثناء الطباعة: ' . $e->getMessage());
        }
    }
    ################### End Print BarCode ################

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
}
