<?php

namespace App\Support;

use Illuminate\Http\Request;

class AdminFilters
{
    public static function apply($query, Request $request, array $columns, string $status = 'status')
    {
        $data = $request->validate(['page' => 'sometimes|integer|min:1|max:2147483647', 'search' => 'nullable|string|max:255', 'status' => 'nullable|integer|between:0,5']);
        if (! empty($data['search'])) {
            $query->where(function ($q) use ($columns, $data) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', '%'.$data['search'].'%');
                }
            });
        }
        if (isset($data['status'])) {
            $query->where($status, $data['status']);
        }

        return $query;
    }
}
