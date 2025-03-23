<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'brief'=>$this->brief,
            'description'=>$this->description,
            'status'=>$this->status,
            'location'=>$this->location,
            'views_number'=>$this->views_number,
            'rating'=>$this->rating,
            'video'=>$this->video,
            'price'=>$this->price,
            'service_date'=>$this->service_date,
            'service_time_from'=>$this->service_time_from,
            'service_time_to'=>$this->service_time_to,
            'gender'=>$this->gender,
            'category'=> $this->category,
            'user'=>$this->user,
            'images'=>$this->images,
            'comments'=>$this->comments,
            // 'favorites'=> FavoriteResource::collection($this->favorites),
            'favorite'=>$this->favorite
        ];
    }
}
