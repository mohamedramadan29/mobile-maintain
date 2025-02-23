<?php

namespace App\Http\Controllers\dashboard;

use Exception;
use Mpdf\Mpdf;
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
use Intervention\Image\Facades\Image;
use App\Models\dashboard\InvoiceCheck;
use App\Models\dashboard\InvoiceImage;
use App\Models\dashboard\InvoiceSteps;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\dashboard\ProblemCategory;
use Illuminate\Support\Facades\Validator;
// use Intervention\Image\Facades\Image;
class InvoiceController extends Controller
{
    use Message_Trait;
    use Upload_Images;

    public function index()
    {
        $invoices = Invoice::orderBy('id', 'desc')->paginate(10);
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
                //  dd($data);
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
        return view('dashboard.invoices.create', compact('problems', 'checks'));
    }

    public function update(Request $request, $id)
    {
        try {
            if ($request->isMethod('post')) {
                $data = $request->all();
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
                $invoice = Invoice::find($id);
                $invoice->name = $data['name'];
                $invoice->phone = $data['phone'];
                $invoice->title = $data['title'];
                $invoice->problems = json_encode($data['problems']);
                $invoice->description = $data['description'];
                $invoice->price = $data['price'];
                $invoice->date_delivery = $data['date_delivery'];
                $invoice->time_delivery = $data['time_delivery'];
                $invoice->status = $data['status'];
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
                        $checkResult = InvoiceCheck::updateOrCreate(
                            [
                                'invoice_id' => $invoice->id,
                                'problem_id' => $problem_id,
                            ],
                            [
                                'work' => $data['work_' . $problem_id][0] ?? null,
                                'notes' => $data['notes'][$index] ?? '',
                                'after_check' => $data['after_check'][$index] ?? '',
                            ]
                        );
                    }
                }

                DB::commit();
                return $this->success_message(' تم تعديل الفاتورة بنجاح');
            }
        } catch (Exception $e) {
            return $this->exception_message($e);
        }
        $invoice = Invoice::find($id);
        $checks = CheckText::all();
        $problems = ProblemCategory::all();
        return view('dashboard.invoices.update', compact('invoice', 'problems', 'checks'));
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

            // توليد الباركود باستخدام مكتبة Picqer
            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode((string) $invoice->id, $generator::TYPE_CODE_128);
            // إعدادات حجم الورقة بناءً على المحتوى
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'default_font' => 'Zain',
                'format' => [50, 25], // عرض وطول الورقة (80 مم × 150 مم)، يمكن تغييره حسب البيانات
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);
            // إرسال البيانات إلى ملف العرض (View)
            $html = view('dashboard.invoices.barcode_pdf', compact('invoice', 'barcode'))->render();
            // كتابة الـ HTML في PDF
            $mpdf->WriteHTML($html);
            // عرض الـ PDF مباشرة أو تحميله
            return $mpdf->Output("Invoice_{$invoice->id}.pdf", 'I');
        } catch (Exception $e) {
            return back()->withErrors('حدث خطأ أثناء الطباعة: ' . $e->getMessage());
        }
    }
    ################### End Print BarCode ################
}
