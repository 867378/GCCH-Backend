<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function notifyUser($userId, $content, $type, $senderId = null)
    {
        Notification::create([
            'user_id' => $userId,
            'sender_id' => $senderId,
            'content' => $content,
            'type' => $type,
            'timestamp' => now(),
            'is_read' => false,
        ]);
    }

    public function userNotifications()
    {
        $authId = Auth::id();

        $notifications = Notification::where('user_id', $authId)
            ->orderBy('timestamp', 'desc')
            ->get();

        return response()->json([
            'notifications'=>$notifications
        ]);
    }
}
