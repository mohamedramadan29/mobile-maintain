<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Traits\Message_Trait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use Message_Trait;
    public function AllRead()
    {
        //$user = auth()->user();
        $user = Auth::guard('admin')->user();
        $user->unreadNotifications->markAsRead();
        return $this->success_message(' تم قراءة جميع الاشعارات  ');
        //  return response()->json(['message' => 'All notifications marked as read']);
    }
}
