<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Models\dashboard\Setting;
use App\Http\Traits\Message_Trait;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    use Message_Trait;

    public function index()
    {
        $setting = Setting::first();
        return view('dashboard.setting.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();
        $setting->update(
            [
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'terms' => $request->terms,
            ]
        );
        return $this->success_message('تم التحديث بنجاح');
    }
}
