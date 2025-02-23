<?php

namespace App\Http\Controllers\dashboard;

use Illuminate\Http\Request;
use App\Models\dashboard\Message;
use App\Http\Traits\Message_Trait;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    use Message_Trait;

    public function index()
    {
        $messages = Message::all();
        return view('dashboard.messages.index', compact('messages'));
    }
    public function update(Request $request, $id)
    {
        $message = Message::where('id', $id)->first();
        if ($request->isMethod('post')) {

            $data = $request->all();
            $message->update([
                'message_type' => $data['message_type'],
                'template_text' => $data['template_text'],
            ]);

            return $this->success_message(' تم تعديل الرسالة بنجاح  ');
        }
        return view('dashboard.messages.update', compact('message'));
    }
}
