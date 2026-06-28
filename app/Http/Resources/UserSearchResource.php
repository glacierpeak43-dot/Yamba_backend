<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserSearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = User::find(Auth::user()->id);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => new ProfileResource($this->profile),
            'role' => $this->role,
            'university' => $this->university,
            'is_verified' => $this->email_verified_at != null ? true : false,
            //is friend flag
            'type' => $this->type,
            'is_friend' => $user->isFriend($user->id, $this->id),
            'chat_id' => $user->isFriend($user->id, $this->id) ? $user->getChatWithUser($this->id)->id : null,
            'is_friend_request_sent' => $user->friendRequestAreadySent($user->id, $this->id),
            'is_friend_request_received' => $user->friendRequestAreadyReceived($user->id, $this->id),
            'friend_request_id' => $user->friendRequestAreadySent($user->id, $this->id) ||  $user->friendRequestAreadyReceived($user->id, $this->id) ?  $user->friendRequest($this->id)->id : null,
            // 'myAppointments' => $this->myAppointments,
            // 'counsellorAppointments' => $this->counsellorAppointments,
        ];
    }
}
