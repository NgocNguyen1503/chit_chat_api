<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $totalRooms = 50;

        for ($i = 0; $i < $totalRooms; $i++) {
            $numUsers = rand(2, 5);

            if ($numUsers === 2) {
                $friendship = DB::table('friends')
                    ->inRandomOrder()
                    ->first();

                if (!$friendship) {
                    continue;
                }

                $userIds = [$friendship->user_id, $friendship->friend_id];
            } else {
                $userIds = [];

                while (count($userIds) < $numUsers) {
                    $id = rand(1, 50);
                    if (!in_array($id, $userIds)) {
                        $userIds[] = $id;
                    }
                }
            }

            DB::table('chat_rooms')->insert([
                'users_id' => implode(',', $userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
