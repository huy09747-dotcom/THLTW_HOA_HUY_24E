<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sort_order' => (int) $this->sort_order,
            'name' => $this->name,
            'link' => $this->link,
            'type' => $this->type,
            'table_id' => $this->table_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
