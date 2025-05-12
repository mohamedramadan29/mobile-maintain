<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\Message_Trait;
use App\Models\dashboard\CheckText;

class CheckTextController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $problems = CheckText::all();
        return view('dashboard.checktext.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:check_texts,name',
            ]);

            CheckText::create([
                'name' => $request->name
            ]);
            return redirect()->route('dashboard.checktexts.index')
                ->with('Success_message', 'تم اضافة نص الفحص بنجاح');
        }
        return view('dashboard.checktext.create_page');
    }

    public function update(Request $request, $id)
    {
        $problem = CheckText::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.checktexts.index')
                ->with('Error_message', 'نص الفحص غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:check_texts,name,' . $problem->id,
            ]);
            $problem->name = $request->name;
            $problem->save();
            return redirect()->route('dashboard.checktexts.index')
                ->with('Success_message', 'تم تعديل نص الفحص بنجاح');
        }

        return view('dashboard.checktext.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = CheckText::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.checktexts.index')
                ->with('Error_message', 'نص الفحص غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.checktexts.index')
                ->with('Success_message', 'تم حذف نص الفحص بنجاح');
        }

        return view('dashboard.checktext.delete_page', compact('problem'));
    }

}
