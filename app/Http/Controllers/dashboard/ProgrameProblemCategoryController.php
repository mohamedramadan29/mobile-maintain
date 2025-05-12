<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\Message_Trait;
use App\Models\dashboard\ProgrameProblemCategory;

class ProgrameProblemCategoryController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $problems = ProgrameProblemCategory::orderBy("created_at","desc")->paginate(10);
        return view('dashboard.programe_problem_category.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:programe_problem_categories,name',
                'solved_time'=>'required'
            ]);
            ProgrameProblemCategory::create([
                'name' => $request->name,
                'solved_time'=>$request->solved_time,
            ]);
            return redirect()->route('dashboard.programe_problem_categories.index')
                ->with('Success_message', 'تم اضافة القسم بنجاح');
        }
        return view('dashboard.programe_problem_category.create_page');
    }

    public function update(Request $request, $id) {
        $problem = ProgrameProblemCategory::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.programe_problem_categories.index')
                ->with('Error_message', 'القسم غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:programe_problem_categories,name,' . $problem->id,
                'solved_time' => 'required'
            ]);
            $problem->name = $request->name;
            $problem->solved_time = $request->solved_time;
            $problem->save();
            return redirect()->route('dashboard.programe_problem_categories.index')
                ->with('Success_message', 'تم تعديل القسم بنجاح');
        }

        return view('dashboard.programe_problem_category.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = ProgrameProblemCategory::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.programe_problem_categories.index')
                ->with('Error_message', 'القسم غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.programe_problem_categories.index')
                ->with('Success_message', 'تم حذف القسم بنجاح');
        }

        return view('dashboard.programe_problem_category.delete_page', compact('problem'));
    }
}
