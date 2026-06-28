<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class MessageResource extends JsonResource
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
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'from' => $this->from,
            'message' => $this->message,
            'attachement_url' => $this->attachement_url,
            'deleted_that_message' => $this->deletedThatMessage(),
            'deleted_by_user' => $this->deletedByUser(),
            'is_read' => $this->is_read,
            'created_at' => $this->created_at->diffForHumans(),
            'created_at_data' => $this->created_at,
            'deleted_at_by_user_1' => $this->deleted_at_by_user_1,
            'deleted_at_by_user_2' => $this->deleted_at_by_user_2,
            'is_your_message' => $this->from == Auth::user()->id,
        ];
    }
}
