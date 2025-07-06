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
            $userChatRooms = collect($userChatRooms)->sortByDesc('created_at')->values();
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
            ->where('id', '!=', Auth::user()->id)
            ->get()->pluck('name')->implode(', ');
        $chatroom->chatroom_type = collect(explode(',', $chatroom->users_id))->count() > 2 ? 'group' : 'friend';
        foreach ($chatroom->messages as $message) {
            $message->_created_at = Carbon::parse($message->created_at)->format('h:i A');
            $message->type = $message->user_id != Auth::user()->id ? 'friend' : 'me';
            unset($message->user_id, $message->chatroom_id, $message->created_at);
        }
        unset($chatroom->id);
        return ApiResponse::success($chatroom);
    }

    public function sendMessage(Request $request)
    {
        $params = $request->all();
        $message = Message::create([
            'user_id' => Auth::user()->id,
            'chatroom_id' => $params['chatroom_id'],
            'message' => $params['message'],
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $message->user_name = Auth::user()->name;
        $message->type = 'me';
        $message->_created_at = Carbon::parse($message->created_at)->format('h:i A');
        unset($message->created_at);
        return ApiResponse::success($message);
    }
}
