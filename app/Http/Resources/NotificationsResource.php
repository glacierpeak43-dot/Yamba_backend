<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'type'=>$this->type,
            'message'=>$this->message,
            'friend_request'=> $this->friendRequest,
            'location'=>$this->location,
            'created_at'=>$this->created_at,
            'time'=>$this->created_at->diffForHumans(),
            'read'=> $this->read

        ];
    }
}
