<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;
    protected $fillable=[
        'from_user',
        'to_user',
        'content',
        'chat_id',
        'attachment'
    ];

    public function chat():BelongsTo{
        return $this->belongsTo(Chat::class,'chat_id');
    }
    public function to_user():BelongsTo{
        return $this->belongsTo(User::class,'to_user');
    }
    public function from_user():BelongsTo{
        return $this->belongsTo(User::class,'from_user');
    }
}
