<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQCategoryResource extends JsonResource
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
            'created_at'=>$this->created_at,
            'faq_subcategories'=> FAQSubcategoryResource::collection($this->faqSubcategories)
        ];
    }
}
