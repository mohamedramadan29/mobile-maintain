<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\Message_Trait;
use App\Models\dashboard\ProblemCategory;
use Illuminate\Http\Request;

class ProblemCategoryController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $problems = ProblemCategory::all();
        return view('dashboard.problems.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:problem_categories,name',
                'solved_time'=>'required'
            ]);
            ProblemCategory::create([
                'name' => $request->name,
                'solved_time'=>$request->solved_time,
            ]);
            return redirect()->route('dashboard.problem_categories.index')
                ->with('Success_message', 'تم اضافة القسم بنجاح');
        }
        return view('dashboard.problems.create_page');
    }

    public function update(Request $request, $id) {
        $problem = ProblemCategory::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.problem_categories.index')
                ->with('Error_message', 'القسم غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:problem_categories,name,' . $problem->id,
                'solved_time' => 'required'
            ]);
            $problem->name = $request->name;
            $problem->solved_time = $request->solved_time;
            $problem->save();
            return redirect()->route('dashboard.problem_categories.index')
                ->with('Success_message', 'تم تعديل القسم بنجاح');
        }

        return view('dashboard.problems.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = ProblemCategory::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.problem_categories.index')
                ->with('Error_message', 'القسم غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.problem_categories.index')
                ->with('Success_message', 'تم حذف القسم بنجاح');
        }

        return view('dashboard.problems.delete_page', compact('problem'));
    }


}
