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
        $problems =PieceSource::all();
        return view('dashboard.piece_resources.index', compact('problems'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:check_texts,name',
        ]);

        PieceSource::create(
            ['name' => $request->name]
        );
        return $this->success_message(' تم اضافة   بنجاح');
    }

    public function update(Request $request, $id){
        $data = $request->all();
        $problem = PieceSource::find($id);
        $problem->name = $data['name'];
        $problem->save();
        return $this->success_message(' تم تعديل   بنجاح');
    }

    public function destroy($id)
    {
        $problem = PieceSource::find($id);

        $problem->delete();
        return $this->success_message(' تم حذف   بنجاح');
    }
}
