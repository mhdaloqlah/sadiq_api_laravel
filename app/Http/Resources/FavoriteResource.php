<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'service_id'=>$this->service_id,
            'service'=> new ServiceResource($this->service),
            'user_id'=>$this->user_id,
           
            'user'=>$this->user,
            
            
            
        ];
    }
}
