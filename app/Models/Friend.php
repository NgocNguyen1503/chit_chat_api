<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    const FRIEND_AWAITING = 1;
    const FRIEND_ACCEPTED = 2;
    const FRIEND_REJECTED = 3;

    protected $table = 'friends';

    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    protected $cast = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];
}
