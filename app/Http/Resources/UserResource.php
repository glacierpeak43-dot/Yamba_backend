<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'profile' => new ProfileResource($this->profile),
            'role' => $this->role,
            'university' => $this->university,
            'is_verified' => $this->email_verified_at != null ? true : false,
            'forum'=>$this->has_agreed_forum == 0 ? false : true,
            //is friend flag
            'type' => $this->type,
            'is_blocked' => $this->is_blocked_from_system == 0 ? false : true
            // 'myAppointments' => $this->myAppointments,
            // 'counsellorAppointments' => $this->counsellorAppointments,
        ];
    }
}
