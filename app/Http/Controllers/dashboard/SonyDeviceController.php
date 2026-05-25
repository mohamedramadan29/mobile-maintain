<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\dashboard\SonyDevice;
use App\Http\Traits\Message_Trait;
use Illuminate\Http\Request;

class SonyDeviceController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $problems = SonyDevice::all();
        return view('dashboard.sony_device.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:sony_devices,name',
            ]);

            SonyDevice::create([
                'name' => $request->name
            ]);
            return redirect()->route('dashboard.sony_devices.index')
                ->with('Success_message', 'تم اضافة جهاز سوني بنجاح');
        }
        return view('dashboard.sony_device.create_page');
    }

    public function update(Request $request, $id)
    {
        $problem = SonyDevice::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.sony_devices.index')
                ->with('Error_message', 'جهاز سوني غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:sony_devices,name,' . $problem->id,
            ]);
            $problem->name = $request->name;
            $problem->save();
            return redirect()->route('dashboard.sony_devices.index')
                ->with('Success_message', 'تم تعديل جهاز سوني بنجاح');
        }

        return view('dashboard.sony_device.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = SonyDevice::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.sony_devices.index')
                ->with('Error_message', 'جهاز سوني غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.sony_devices.index')
                ->with('Success_message', 'تم حذف جهاز سوني بنجاح');
        }

        return view('dashboard.sony_device.delete_page', compact('problem'));
    }
}
