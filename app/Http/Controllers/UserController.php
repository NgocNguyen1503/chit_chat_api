<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                    $chatRoom->chatroom_name = implode(
                        ', ',
                        User::whereIn('id', $listUser)
                            ->where('id', '!=', Auth::user()->id)
                            ->pluck('name')
                            ->toArray()
                    );
                    $userChatRooms[] = $chatRoom;
                }
            }
            return ApiResponse::success($userChatRooms);
        } catch (\Throwable $th) {
            return ApiResponse::unprocessableContent();
        }
    }

    public function listMessage(Request $request)
    {
        $params = $request->all();
        $chatroom = ChatRoom::select(['id', 'thumbnail', 'users_id'])
            ->where('id', $params['chatroom_id'])
            ->with([
                'messages' => fn($q) => $q->select(['messages.id', 'messages.user_id', 'messages.chatroom_id', 'messages.message', 'messages.created_at', 'users.id as user_id', 'users.name as user_name'])
                    ->join('users', 'messages.user_id', '=', 'users.id')->orderBy('created_at', 'asc')
            ])->first();
        $chatroom->chatroom_name = User::select(['id', 'name'])
            ->whereIn('id', explode(",", $chatroom->users_id))
            ->get()->pluck('name')->implode(', ');
        collect(explode(',', $chatroom->users_id))->count() > 2 ? $chatroom->chatroom_type = 'group' : $chatroom->chatroom_type = 'friend';
        foreach ($chatroom->messages as $message) {
            $message->_created_at = Carbon::parse($message->created_at)->format('h:i A');
            $message->user_id != Auth::user()->id ? $message->type = 'friend' : $message->type = 'me';
            unset($message->user_id, $message->chatroom_id, $message->created_at);
        }
        unset($chatroom->users_id, $chatroom->id);
        return ApiResponse::success($chatroom);
    }
}
