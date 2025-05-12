<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\Message_Trait;
use App\Models\dashboard\ProgrameDevice;
use Illuminate\Http\Request;

class ProgrameDeviceController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $problems = ProgrameDevice::all();
        return view('dashboard.programe_device.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:programe_devices,name',
            ]);

            ProgrameDevice::create([
                'name' => $request->name
            ]);
            return redirect()->route('dashboard.programe_devices.index')
                ->with('Success_message', 'تم اضافة جهاز البرمجة بنجاح');
        }
        return view('dashboard.programe_device.create_page');
    }

    public function update(Request $request, $id)
    {
        $problem = ProgrameDevice::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.programe_devices.index')
                ->with('Error_message', 'جهاز البرمجة غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:programe_devices,name,' . $problem->id,
            ]);
            $problem->name = $request->name;
            $problem->save();
            return redirect()->route('dashboard.programe_devices.index')
                ->with('Success_message', 'تم تعديل جهاز البرمجة بنجاح');
        }

        return view('dashboard.programe_device.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = ProgrameDevice::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.programe_devices.index')
                ->with('Error_message', 'جهاز البرمجة غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.programe_devices.index')
                ->with('Success_message', 'تم حذف جهاز البرمجة بنجاح');
        }

        return view('dashboard.programe_device.delete_page', compact('problem'));
    }
}
