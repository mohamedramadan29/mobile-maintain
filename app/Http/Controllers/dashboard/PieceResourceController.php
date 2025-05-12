<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Traits\Message_Trait;
use App\Models\dashboard\PieceSource;
use Illuminate\Http\Request;

class PieceResourceController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $problems = PieceSource::all();
        return view('dashboard.piece_resources.index', compact('problems'));
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:piece_sources,name',
            ]);

            PieceSource::create([
                'name' => $request->name
            ]);
            return redirect()->route('dashboard.piece_resources.index')
                ->with('Success_message', 'تم اضافة موارد القطع بنجاح');
        }
        return view('dashboard.piece_resources.create_page');
    }

    public function update(Request $request, $id)
    {
        $problem = PieceSource::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.piece_resources.index')
                ->with('Error_message', 'موارد القطع غير موجود');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|unique:piece_sources,name,' . $problem->id,
            ]);
            $problem->name = $request->name;
            $problem->save();
            return redirect()->route('dashboard.piece_resources.index')
                ->with('Success_message', 'تم تعديل موارد القطع بنجاح');
        }

        return view('dashboard.piece_resources.update_page', compact('problem'));
    }

    public function destroy(Request $request, $id)
    {
        $problem = PieceSource::find($id);
        if (!$problem) {
            return redirect()->route('dashboard.piece_resources.index')
                ->with('Error_message', 'موارد القطع غير موجود');
        }

        if ($request->isMethod('post')) {
            $problem->delete();
            return redirect()->route('dashboard.piece_resources.index')
                ->with('Success_message', 'تم حذف موارد القطع بنجاح');
        }

        return view('dashboard.piece_resources.delete_page', compact('problem'));
    }
}
