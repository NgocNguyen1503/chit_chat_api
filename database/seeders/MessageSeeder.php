<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chatRooms = DB::table('chat_rooms')->get();

        foreach ($chatRooms as $chatRoom) {
            $userIds = explode(',', $chatRoom->users_id);
            $userIds = array_map('trim', $userIds);

            $numMessages = rand(5, 20);

            for ($i = 0; $i < $numMessages; $i++) {
                DB::table('messages')->insert([
                    'user_id' => $userIds[array_rand($userIds)],
                    'chatroom_id' => $chatRoom->id,
                    'message' => fake()->text(50),
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
