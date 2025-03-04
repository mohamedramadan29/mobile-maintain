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
        $request->validate([
            'name' => 'required|unique:check_texts,name',
        ]);

        SpeedDevice::create(
            ['name' => $request->name]
        );
        return $this->success_message(' تم اضافة   بنجاح');
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $problem = SpeedDevice::find($id);
        $problem->name = $data['name'];
        $problem->save();
        return $this->success_message(' تم تعديل   بنجاح');
    }

    public function destroy($id)
    {
        $problem = SpeedDevice::find($id);

        $problem->delete();
        return $this->success_message(' تم حذف   بنجاح');
    }
}
