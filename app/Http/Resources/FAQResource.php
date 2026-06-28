<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQResource extends JsonResource
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
            'question'=> $this->question,
            'key'=>$this->key,
            'created_at'=>$this->created_at,
            'answers'=> $this->answers
        ];
    }
}
