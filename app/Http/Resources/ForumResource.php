<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ForumResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'user' => $this->user,
            'body' => $this->body,
            'forum_category_id' => $this->forum_category_id,
            'created_at' => $this->created_at,
//            'comments' => ForumCommentResource::collection($this->comments)
        ];
    }
}
