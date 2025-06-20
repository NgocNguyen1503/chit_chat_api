<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FriendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $userId = rand(1, 50);
            $friendId = rand(1, 50);

            while ($friendId === $userId) {
                $friendId = rand(1, 50);
            }

            DB::table('friends')->insert([
                'user_id' => $userId,
                'friend_id' => $friendId,
                'status' => rand(1, 3),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
