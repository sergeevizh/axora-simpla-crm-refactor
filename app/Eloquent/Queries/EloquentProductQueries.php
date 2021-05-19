<?php

namespace App\Eloquent\Queries;

use App\Eloquent\Models\Product;
use App\Repositories\IProductDBRepository;

class EloquentProductQueries implements IProductDBRepository
{
    public function get(string $filter = ''): array
    {
        if (isset($filter) == 'new') {
            $products = Product::where('new', 1)->get();
        }
        if (isset($filter) == 'featured') {
            $products = Product::where('featured', 1)->get();
        }
        if (isset($filter) == 'discounted') {
            $products = Product::where('discounted', 1)->limit(10)->get();
        }
        $products = Product::get();

        return $products ? $products->toArray() : [];
    }
}
