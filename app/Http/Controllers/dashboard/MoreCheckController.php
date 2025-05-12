<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\Message_Trait;
use App\Models\dashboard\InvoiceMoreCheck;
use Illuminate\Http\Request;

class MoreCheckController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $problems =InvoiceMoreCheck::all();
        return view('dashboard.more_check.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:invoice_more_checks,name',
            ]);

            InvoiceMoreCheck::create([
                'name' => $request->name
            ]);
            return redirect()->route('dashboard.more_checks.index')
                ->with('Success_message', 'تم اضافة الفحص الاضافي بنجاح');
        }
        return view('dashboard.more_check.create_page');
    }

    public function update(Request $request, $id)
    {
        $problem = InvoiceMoreCheck::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.more_checks.index')
                ->with('Error_message', 'الفحص الاضافي غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:invoice_more_checks,name,' . $problem->id,
            ]);
            $problem->name = $request->name;
            $problem->save();
            return redirect()->route('dashboard.more_checks.index')
                ->with('Success_message', 'تم تعديل الفحص الاضافي بنجاح');
        }

        return view('dashboard.more_check.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = InvoiceMoreCheck::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.more_checks.index')
                ->with('Error_message', 'الفحص الاضافي غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.more_checks.index')
                ->with('Success_message', 'تم حذف الفحص الاضافي بنجاح');
        }

        return view('dashboard.more_check.delete_page', compact('problem'));
    }
}
