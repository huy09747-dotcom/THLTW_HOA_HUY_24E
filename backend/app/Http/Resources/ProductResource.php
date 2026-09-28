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
            'danhmuc_id' => $this->danhmuc_id,
            'thuonghieu_id' => $this->thuonghieu_id,
            'tensanpham' => $this->tensanpham,
            'hinhanh' => $this->hinhanh,
            'gia' => $this->gia,
            'giakhuyenmai' => $this->giakhuyenmai,
            'mota' => $this->mota,
            'soluongton' => $this->soluongton,
            'trangthai' => $this->trangthai,
            'created_at' => $this->created_at,
        ];
    }
}