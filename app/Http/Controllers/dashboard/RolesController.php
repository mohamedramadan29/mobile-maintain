<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Traits\Message_Trait;
use Illuminate\Http\Request;
use App\Models\dashboard\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    use Message_Trait;
    public function index()
    {
        $roles = Role::all();
        return view('dashboard.roles.index',compact('roles'));
    }

    public function create(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            $rules = [
                'role' => 'required',
                'permissions' => 'required',
            ];
            $messages = [
                'role.required' => 'من فضلك ادخل اسم الصلاحية ',
                'permissions.required' => 'من فضلك ادخل صلاحيات الصلاحية ',
            ];
            $validator = Validator::make($data,$rules,$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $role = new Role();
            $role->role = $data['role'];
            $role->permission = json_encode($data['permissions']);
            $role->save();

            return $this->success_message(' تم اضافة الصلاحية بنجاح');
        }
        return view('dashboard.roles.create');
    }

    public function update(Request $request, $id){
        if($request->isMethod('post')){
            $data = $request->all();
            $rules = [
                'role' => 'required',
                'permissions' => 'required',
            ];
            $messages = [
                'role.required' => 'من فضلك ادخل اسم الصلاحية ',
                'permissions.required' => 'من فضلك ادخل صلاحيات الصلاحية ',
            ];
            $validator = Validator::make($data,$rules,$messages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $role = Role::find($id);
            $role->role = $data['role'];
            $role->permission = json_encode($data['permissions']);
            $role->save();
            return $this->success_message(' تم تعديل الصلاحية بنجاح');
        }
        $role = Role::find($id);
        return view('dashboard.roles.edit',compact('role'));
    }

    public function destroy(Request $request,$id){
        $role = Role::find($id);
        if($request->isMethod('post')){

            if(!$role || $role->admins()->count() > 0){
                return $this->error_message('لا يمكن حذف الصلاحية لانها مرتبطة بالادمن');
            }
            $role->delete();
            return redirect()->route('dashboard.roles.index')->with('Success_message', ' تم حذف الصلاحية بنجاح');
        }
        return view('dashboard.roles.delete',compact('role'));
    }
}
