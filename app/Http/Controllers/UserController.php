<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\ChatRoom;
use App\Models\Friend;
use App\Models\Message;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function listFriendInfo()
    {
        try {
            $friends = Friend::where('user_id', Auth::user()->id)
                ->where('status', Friend::FRIEND_ACCEPTED)
                ->rightJoin('users', 'users.id', '=', 'friends.friend_id')
                ->get();
            foreach ($friends as $friend) {
                $chatRoom = ChatRoom::where('users_id', 'like', '%[' . Auth::user()->id . ',' . $friend->friend_id . ']%')
                    ->orWhere('users_id', 'like', '%[' . $friend->friend_id . ',' . Auth::user()->id . ']%')
                    ->first();
                $friend->chat_room = $chatRoom;

                if ($friend->chat_room) {
                    $friend->last_message = Message::where('chatroom_id', $friend->chat_room->id)
                        ->orderBy('created_at', 'DESC')
                        ->first();
                } else {
                    $friend->last_message = null;
                }
            }
            return ApiResponse::success($friends);
        } catch (\Throwable $th) {
            return ApiResponse::unprocessableContent();
        }
    }
}
