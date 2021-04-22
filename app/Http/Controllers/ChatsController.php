<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Events\MessageSentEvent;
use App\Models\Message;
use App\Models\ResponseModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatsController extends Controller
{
    /**
     * Get all messages
     */
    public function getMessages()
    {
        return ResponseModel::success(
            Message::with('sender:id,name,email', 'receiver:id,name,email')
                ->where('sender_id', Auth::user()->id)
                ->orWhere('receiver_id', Auth::user()->id)
                ->get()
        );
    }

    public function getFriendList()
    {
        $user = Auth::user();

        $data =  Message::with('sender:id,name,email', 'receiver:id,name,email')
            ->where('sender_id', Auth::user()->id)
            ->orWhere('receiver_id', Auth::user()->id)
            ->get();

        $friend_list = [];
        foreach ($data as $item) {
            if ($item->sender_id == $user->id) {
                array_push($friend_list, $item->receiver);
            } else {
                array_push($friend_list, $item->sender);
            } 
        }

        return ResponseModel::success(
            array_values(array_unique($friend_list))
        );
    }

    public function getChatHistory(Request $request)
    {
        $user = Auth::user();

        return ResponseModel::success(
            Message::with('sender:id,name', 'receiver:id,name')
                ->where(function ($q) use ($request, $user) {
                    $q->where('sender_id', $user->id);
                    $q->where('receiver_id', $request->chat_with_user_id);
                })
                ->orWhere(function ($q) use ($request, $user) {
                    $q->where('sender_id', $request->chat_with_user_id);
                    $q->where('receiver_id', $user->id);
                })
                ->get()
                ->map(function ($item) {
                    if ($item->sender_id == Auth::user()->id) {
                        $item->type = 'out';
                    } else {
                        $item->type = 'in';
                    }
                    return $item;
                })
        );
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseModel::failed($validator->errors());
        }

        $user = Auth::user();

        broadcast(new MessageEvent($request->message, $request->receiver_id))->toOthers();

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        return ResponseModel::success([
            'message' => 'Message sent!'
        ]);
    }
}
