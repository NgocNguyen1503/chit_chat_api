<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    const UNVIEW = 1;
    const VIEWED = 2;
    const REVOKED = 3;

    protected $table = 'messages';

    protected $fillable = [
        'user_id',
        'chatroom_id',
        'message',
        'status',
    ];

    protected $cast = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];
}
