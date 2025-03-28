<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','user_to'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function user_to(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_to');
    }

    public function messages():HasMany{
        return $this->hasMany(Message::class);
    }
}
