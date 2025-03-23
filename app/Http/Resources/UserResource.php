<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name'=>$this->first_name,
            'last_name'=>$this->last_name,
            'email'=>$this->email,
            'email_verified_at'=>$this->email_verified_at,
            'phone'=>$this->phone,
            'birth_date'=>$this->birth_date,
            'about'=>$this->about,
            'image_profile'=>$this->image_profile,
            'image_id_front'=>$this->image_id_front,
            'image_id_back'=>$this->image_id_back,
            'longitude'=>$this->longitude,
            'latitude'=>$this->latitude,
            'status'=>$this->status,
            'services'=> ServiceResource::collection($this->services),
            'favorites'=> FavoriteResource::collection($this->favorites),
            'chats'=> $this->chats,
            'platform'=>$this->platform,
            'device_token'=>$this->device_token
        ];
    }
}
