<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,

            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand?->name,

            'name' => $this->name,
            'slug' => $this->slug,

            'price_buy' => $this->price_buy,
            'price_sale' => $this->price_sale,
            'discount_percent' => (int) $this->discount_percent,
            'final_price' => $this->final_price,

            'image' => $this->image,
            'qty' => $this->qty,

            'detail' => $this->detail,
            'description' => $this->description,

            'status' => $this->status,

            'sku' => $this->sku,
            'unit' => $this->unit,
            'prep_minutes' => $this->prep_minutes,

            'is_available' => $this->is_available,
            'is_featured' => $this->is_featured,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}