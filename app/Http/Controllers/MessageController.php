<?php

namespace App\Http\Controllers;

use App\Models\Message;
//use App\Events\MessageSent;
//use App\Events\MessageRead;
use App\Http\Controllers\NotificationController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function send(Request $request){
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'timestamp' => now(),
            'is_read' => false,
        ]);

        //broadcast(new MessageSent($message))->toOthers();

        //Fetch the sender's information for notification
        $notifier=new NotificationController();
        $senderId = Auth::id();
        $senderInfo = User::find($senderId);

        $senderName = '';
        if ($senderInfo->role === 'applicant' && $senderInfo->applicant) {
            $senderName = $senderInfo->applicant->first_name . ' ' . $senderInfo->applicant->last_name;
        } elseif ($senderInfo->role === 'company' && $senderInfo->company){
            $senderName = $senderInfo->company->company_name;
        }

        //Notifies the receiver
        $notifier->notifyUser($request->receiver_id, 'You have received a message from ' . $senderName, 'message', $senderId);

        return response()->json($message, 200);
    }

    public function conversation($userId){
        $authUserId = Auth::id();

        $messages = Message::where(function($query) use ($authUserId, $userId) {
            $query->where('sender_id', $authUserId)
                  ->where('receiver_id', $userId);
        })->orWhere(function($query) use ($authUserId, $userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $authUserId);
        })->orderBy('timestamp', 'asc')->get();

        return response()->json($messages, 200);
    }

    public function markAsRead($messageId){
        $message = Message::where('id', $messageId)
            ->where('receiver_id', Auth::id())
            ->firstOrFail();
            

        //broadcast(new MessageRead($message))->toOthers();

        return response()->json(['message' => 'Message marked as read'], 200);
    }   
}
