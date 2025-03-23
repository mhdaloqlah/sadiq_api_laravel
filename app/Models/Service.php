<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'brief',
        'description',
        'status',
        'location',
        'views_number',
        'rating',
        'video',
        'price',
        'service_date',
        'service_time_from',
        'service_time_to',
        'gender',
        'category_id',
        'user_id',
        'favorite'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function images(): HasMany
    {
        return $this->hasMany(ImageService::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'service_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'service_id');
    }

    public function scopePriceBetween($query, $price_from, $price_to): Builder
    {
        return $query->where('price', '>=',$price_from)->where('price','<=',$price_to);
    }

    public function scopeTimeBetween($query, $time_from, $time_to): Builder
    {
        return $query->where('service_time_from', '>=',$time_from)->where('service_time_to','<=',$time_to);
    }
}
