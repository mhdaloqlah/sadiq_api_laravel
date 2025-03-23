<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageService extends Model
{
    use HasFactory;
    protected $fillable = [
        'image',
        'service_id'
    ];

    public function service():BelongsTo{
        return $this->belongsTo(Service::class);
    }
}
