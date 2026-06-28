<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'name' => $this->user->name,
            'email' => $this->user->email,
            'image' => $this->image_path,
            'nick_name' => $this->nick_name,
            'phone' => $this->phone_number,
            'title' => $this->title,
            'bio' => $this->bio,
            'points' => $this->points,
            'level_number' => $this->level_number,
            'level' => $this->getProfileLevel(),
            'articles_views' => $this->articleViews->count(),
        ];
    }
}
