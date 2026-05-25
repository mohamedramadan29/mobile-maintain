<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\Message_Trait;
use App\Models\dashboard\PcDevice;
use Illuminate\Http\Request;

class PcDeviceController extends Controller
{

    use Message_Trait;
    public function index()
    {
        $problems = PcDevice::all();
        return view('dashboard.pc_device.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:pc_devices,name',
            ]);

            PcDevice::create([
                'name' => $request->name
            ]);
            return redirect()->route('dashboard.pc_devices.index')
                ->with('Success_message', 'تم اضافة جهاز سوني بنجاح');
        }
        return view('dashboard.pc_device.create_page');
    }

    public function update(Request $request, $id)
    {
        $problem = PcDevice::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.pc_devices.index')
                ->with('Error_message', 'جهاز كمبيوتر غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:pc_devices,name,' . $problem->id,
            ]);
            $problem->name = $request->name;
            $problem->save();
            return redirect()->route('dashboard.pc_devices.index')
                ->with('Success_message', 'تم تعديل جهاز كمبيوتر بنجاح');
        }

        return view('dashboard.pc_device.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = PcDevice::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.pc_devices.index')
                ->with('Error_message', 'جهاز كمبيوتر غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.pc_devices.index')
                ->with('Success_message', 'تم حذف جهاز كمبيوتر بنجاح');
        }

        return view('dashboard.pc_device.delete_page', compact('problem'));
    }
}
