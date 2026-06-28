<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQSubcategoryResource extends JsonResource
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
            'id'=>$this->id,
            'name'=>$this->name,
            'key'=>$this->key,
            'category_id'=>$this->f_a_q_category_id,
            'created_at'=>$this->created_at,
            'faq'=> FAQResource::collection($this->faq)
        ];
    }
}
