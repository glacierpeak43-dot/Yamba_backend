<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleCommentReplyResource extends JsonResource
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
            'article_id' => $this->article_id,
            'created_at' => $this->created_at,
            'user' => $this->user,
            'comment' => $this->comment,
            'can_be_viewed'=>$this->can_be_viewed == 0 ? false : true,
            'votes' => $this->votes == null ? 0 : $this->votes->count(),
        ];
    }
}
