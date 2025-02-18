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
        $request->validate([
            'name' => 'required|unique:problem_categories,name',
            'solved_time'=>'required'
        ]);
        ProblemCategory::create(
            [
                'name' => $request->name,
                'solved_time'=>$request->solved_time,
                ]
        );
        return $this->success_message(' تم اضافة القسم بنجاح');
    }

    public function update(Request $request, $id){
        $data = $request->all();
        $problem = ProblemCategory::find($id);
        $problem->name = $data['name'];
        $problem->solved_time = $data['solved_time'];
        $problem->save();
        return $this->success_message(' تم تعديل القسم بنجاح');
    }

    public function destroy($id)
    {
        $problem = ProblemCategory::find($id);

        $problem->delete();
        return $this->success_message(' تم حذف القسم بنجاح');
    }


}
