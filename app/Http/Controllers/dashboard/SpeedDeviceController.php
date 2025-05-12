<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\Message_Trait;
use App\Models\dashboard\SpeedDevice;
use Illuminate\Http\Request;

class SpeedDeviceController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $problems = SpeedDevice::all();
        return view('dashboard.speed_device.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:speed_devices,name',
            ]);

            SpeedDevice::create([
                'name' => $request->name
            ]);
            return redirect()->route('dashboard.speed_devices.index')
                ->with('Success_message', 'تم اضافة جهاز السرعة بنجاح');
        }
        return view('dashboard.speed_device.create_page');
    }

    public function update(Request $request, $id)
    {
        $problem = SpeedDevice::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.speed_devices.index')
                ->with('Error_message', 'جهاز السرعة غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:speed_devices,name,' . $problem->id,
            ]);
            $problem->name = $request->name;
            $problem->save();
            return redirect()->route('dashboard.speed_devices.index')
                ->with('Success_message', 'تم تعديل جهاز السرعة بنجاح');
        }

        return view('dashboard.speed_device.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = SpeedDevice::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.speed_devices.index')
                ->with('Error_message', 'جهاز السرعة غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.speed_devices.index')
                ->with('Success_message', 'تم حذف جهاز السرعة بنجاح');
        }

        return view('dashboard.speed_device.delete_page', compact('problem'));
    }
}
