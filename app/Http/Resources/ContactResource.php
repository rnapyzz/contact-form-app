<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'last_name' => $this->last_name,
            'first_name' => $this->first_name,
            'email' => $this->email,
            'tel' => $this->tel,
            'address' => $this->address,
            'building' => $this->building,
            'gender' => $this->gender,
            'detail' => $this->detail,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'content' => $this->category->content,
            ] : null,
            'tags' => $this->tags ? $this->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'content' => $tag->name,
                ];
            }) : [],
            'created_at' => $this->created_at,
        ];
    }
}
