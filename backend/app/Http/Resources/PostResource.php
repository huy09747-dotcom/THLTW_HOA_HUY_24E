<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'topic_id' => $this->topic_id,
            'detail' => $this->detail,
            'image' => $this->image,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'topic_name' => $this->whenLoaded('chude', fn () => $this->chude?->name),
        ];
    }
}
