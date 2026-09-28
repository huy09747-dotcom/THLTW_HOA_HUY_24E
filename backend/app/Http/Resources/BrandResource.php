<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sort_order' => $this->sort_order,
            'description' => $this->description,
            'image' => $this->image,
            'title' => $this->title,
            'content' => $this->content,
            'replay_id' => $this->replay_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'products' => ProductResource::collection(
                $this->whenLoaded('products')
            ),
        ];
    }
}