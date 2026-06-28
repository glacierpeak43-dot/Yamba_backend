<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleReplyVotesResource extends JsonResource
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
            'comment'=>$this->comment,
            'user'=> $this->user,
            'article_comment_id'=> $this->article_comment_id,
            'votes'=>$this->votes->count(),
            'created_at'=>$this->created_at
        ];
    }
}
