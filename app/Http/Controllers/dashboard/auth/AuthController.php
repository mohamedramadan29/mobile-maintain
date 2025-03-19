<?php

namespace App\Http\Controllers\dashboard\auth;

use App\Http\Traits\Message_Trait;
use Illuminate\Http\Request;
use App\Models\dashboard\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;

class AuthController extends Controller
{
    use Message_Trait;
    public static function middleware()
    {
        return [
            new Middleware(middleware: 'guest:admin', except: ['logout']),
        ];
    }

    public function show_login()
    {
        if(Auth::guard('admin')->check()){
            return redirect()->route('dashboard.welcome');
        }
        return view("dashboard.auth.login");
    }
    public function register_login(Request $request)
    {
        if ($request->isMethod("POST")) {
            $data = $request->all();
            $rules = [
                'email' => 'required|email',
                'password' => 'required',
                'g-recaptcha-response' => ['required', 'captcha']
            ];
            $messages = [
                'email.required' => 'يجب ادخال البريد الالكتروني',
                'email.email' => 'يجب ادخال بريد الكتروني صحيح',
                'password.required' => 'يجب ادخال كلمة المرور',
                'g-recaptcha-response.required' => ' من فضلك اكد انك لست روبوت',
                'g-recaptcha-response.captcha' => 'من فضلك اكد انك لست روبوت غير صحيح'

            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->has('remember'))) {
                // return redirect()->route('dashboard.welcome')->with('success','تم تسجيل الدخول بنجاح ');
                ////// intebded = > بيرجعك علي اخر حاجة كنت شغال عليها بعد التسجيل مرة اخري
                return redirect()->intended(route('dashboard.welcome'));
            }
            // return redirect()->route('dashboard.login.show')->withErrors([]'error','لا يوجد حساب بهذة البيانات ');
            return redirect()->back()->withErrors(['لا يوجد حساب بهذة البيانات ']);
        }
    }
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('dashboard.login.show');
    }

    #################### Update User Profile ###################
    public function update_profile(Request $request)
    {

        $user = Admin::find(Auth::guard('admin')->user()->id);

        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'name' => 'required',
                'email' => 'required|email|unique:admins,email,' . $user->id,
                'phone' => 'required',
            ];
            $messages = [
                'name.required' => 'من فضلك ادخل اسم المستخدم ',
                'email.required' => 'من فضلك ادخل البريد الالكتروني ',
                'email.email' => 'من فضلك ادخل بريد الكتروني صحيح ',
                'phone.required' => 'من فضلك ادخل رقم الهاتف ',
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();
            return $this->success_message('تم تحديث البيانات بنجاح');
        }

        return view('dashboard.auth.profile.update', compact('user'));


    }
    ################# Update Admin  Password ###################
    public function update_password(Request $request)
    {
        $user = Admin::find(Auth::guard('admin')->user()->id);
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = [
                'password' => 'required',
                'new_password' => 'required',
                'new_password_confirmation' => 'required|same:new_password',
            ];
            $messages = [
                'password.required' => 'يجب ادخال كلمة المرور القديمة',
                'new_password.required' => 'يجب ادخال كلمة المرور الجديدة',
                'new_password_confirmation.required' => 'يجب ادخال تاكيد كلمة المرور الجديدة',
                'new_password_confirmation.same' => 'كلمة المرور الجديدة غير متطابقة',
            ];
            $validator = Validator::make($data, $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if (!Hash::check($request->password, $user->password)) {
                return redirect()->back()->withErrors(['password' => 'كلمة المرور القديمة غير صحيحة']);
            }

            $user->password = bcrypt($request->new_password);
            $user->save();
            $this->success_message('تم تغيير كلمة المرور بنجاح');
            // return redirect()->route('dashboard.welcome')->with('success', 'تم تغيير كلمة المرور بنجاح');
        }

        return view('dashboard.auth.profile.password', compact('user'));
    }
}
