<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Models\dashboard\Role;
use App\Models\dashboard\Admin;
use App\Models\dashboard\Invoice;
use App\Http\Traits\Message_Trait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\dashboard\ProblemCategory;
use App\Models\dashboard\ProgrameProblemCategory;
use App\Models\dashboard\SpeedProblemCategory;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    use Message_Trait;

    public function index()
    {
        $admins = Admin::all();
        return view('dashboard.admins.index', compact('admins'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:admins,email',
                'phone' => 'required|unique:admins,phone',
                'password' => 'required',
                'password_confirmation' => 'required|same:password',
                'role_id' => 'required',
                'type' => 'required',
            ];
            $messages = [
                'name.required' => 'من فضلك ادخل اسم المستخدم ',
                'email.required' => 'من فضلك ادخل البريد الالكتروني ',
                'email.email' => 'من فضلك ادخل بريد الكتروني صحيح ',
                'email.unique' => ' البريد الاكتروني مستخدم من قبل  ',
                'phone.required' => 'من فضلك ادخل رقم الهاتف ',
                'phone.unique' => ' رقم الهاتف متواجد من قبل  ',
                'password.required' => 'من فضلك ادخل كلمة المرور ',
                'password_confirmation.required' => 'من فضلك ادخل تاكيد كلمة المرور ',
                'password_confirmation.same' => 'كلمة المرور غير متطابقة ',
                'role_id.required' => 'من فضلك ادخل صلاحيات المستخدم ',
                'type.required' => 'من فضلك ادخل نوع المستخدم ',
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $admin = new Admin();
            $admin->name = $data['name'];
            $admin->email = $data['email'];
            $admin->phone = $data['phone'];
            $admin->password = Hash::make($data['password']);
            $admin->role_id = $data['role_id'];
            $admin->status = $data['status'];
            $admin->type = $data['type'];
            $admin->save();
            return $this->success_message('تم اضافة المستخدم بنجاح');
        }
        $roles = Role::all();
        return view('dashboard.admins.create', compact('roles'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:admins,email,' . $admin->id,
                'phone' => 'required|unique:admins,phone,' . $admin->id,
                'role_id' => 'required',
                'type' => 'required',
            ];
            $messages = [
                'name.required' => 'من فضلك ادخل اسم المستخدم ',
                'email.required' => 'من فضلك ادخل البريد الالكتروني ',
                'email.unique' => ' البريد الاكتروني مستخدم من قبل  ',
                'email.email' => 'من فضلك ادخل بريد الكتروني صحيح ',
                'phone.required' => 'من فضلك ادخل رقم الهاتف ',
                'phone.unique' => ' رقم الهاتف مستخدم من قبل  ',
                'role_id.required' => 'من فضلك ادخل صلاحيات المستخدم ',
                'type.required' => 'من فضلك ادخل نوع المستخدم ',
            ];

            if (isset($data['password']) && $data['password'] != null) {
                $rules = [
                    'password' => 'required',
                    'password_confirmation' => 'required|same:password',
                ];
                $messages = [
                    'password.required' => 'من فضلك ادخل كلمة المرور ',
                    'password_confirmation.required' => 'من فضلك ادخل تاكيد كلمة المرور ',
                    'password_confirmation.same' => 'كلمة المرور غير متطابقة ',
                ];

                $admin->password = Hash::make($data['password']);
                $admin->save();
            }
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $admin->name = $data['name'];
            $admin->email = $data['email'];
            $admin->phone = $data['phone'];
            $admin->role_id = $data['role_id'];
            $admin->type = $data['type'];
            $admin->status = $data['status'];
            $admin->save();
            return $this->success_message('تم تعديل المستخدم بنجاح');
        }
        $admin = Admin::find($id);
        $roles = Role::all();
        return view('dashboard.admins.update', compact('admin', 'roles'));
    }
    public function destroy(Request $request, $id)
    {
        $admin = Admin::find($id);
        if ($request->isMethod('post')) {
            if ($admin->id == 1) {
                return $this->Error_message('لا يمكن حذف المستخدم المدير');
            }
            $admin->delete();
            return redirect()->route('dashboard.admins.index')->with('Success_message', 'تم حذف المستخدم بنجاح');
        }
        return view('dashboard.admins.delete', compact('admin'));
    }

    ################### Tech Admins الفنيين #######################
    public function tech()
    {
        $admins = Admin::where('type', 'فني')->get();
        $problems = ProblemCategory::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        return view('dashboard.admins.tech', compact('admins', 'problems', 'programe_problems', 'speed_problems'));
    }

    public function update_tech(Request $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return $this->Error_message('لا يوجد مستخدم بهذا الرقم');
        }

        if ($request->isMethod('post')) {
            $data = $request->all();
            $admin->device_nums = $data['device_nums'];
            $admin->problems = json_encode($data['problems'] ?? []);
            $admin->save();
            return $this->success_message('تم تعديل الصلاحيات بنجاح');
        }

        $problems = ProblemCategory::all();
        $programe_problems = ProgrameProblemCategory::all();
        $speed_problems = SpeedProblemCategory::all();
        return view('dashboard.admins.update_tech_page', compact('admin', 'problems', 'programe_problems', 'speed_problems'));
    }

    ###################### Get The Tech Invoices #######################
    public function tech_invoices(Request $request, $id)
    {
        // استخراج التاريخين من الطلب
        $startFrom = $request->input('start_from');
        $endTo = $request->input('end_to');

        // التحقق من وجود القيم وتصفية النتائج
        $query = Invoice::where('admin_repair_id', $id);

        if ($startFrom && $endTo) {
            $query->whereBetween('checkout_time', [$startFrom, $endTo]);
        }
        $invoices = $query->orderBy('id', 'desc')->paginate(10);
        // حساب العدد الإجمالي للفواتير في الفترة المحددة
        $totalInvoices = $query->count();

        return view('dashboard.admins.tech_invoices', compact('invoices', 'id', 'totalInvoices'));
    }
}
