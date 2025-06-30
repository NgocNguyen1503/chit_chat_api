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
    public function listChatRoom()
    {
        try {
            $chatRooms = ChatRoom::all();
            $userChatRooms = [];
            foreach ($chatRooms as $chatRoom) {
                $listUser = explode(",", $chatRoom->users_id);
                if (in_array(Auth::user()->id, $listUser)) {
                    $chatRoom->last_message = Message::where('chatroom_id', $chatRoom->id)
                        ->orderBy('created_at', 'DESC')
                        ->first();
                    $userChatRooms[] = $chatRoom;
                }
            }
            return ApiResponse::success($userChatRooms);
        } catch (\Throwable $th) {
            return ApiResponse::unprocessableContent();
        }
    }
}
