<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FriendRequestResource extends JsonResource
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
            "id" => $this->id,
            "accepted" => $this->accepted,
            "from_user_id" => $this->from_user_id,
            "from_user" => new UserSearchResource($this->fromUser),
            "to_user" => new UserSearchResource($this->toUser),
            "to_user_id" => $this->to_user_id,
            "blocked_at_by_user_1" => $this->blocked_at_by_user_1,
            "blocked_at_by_user_2" => $this->blocked_at_by_user_2,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
