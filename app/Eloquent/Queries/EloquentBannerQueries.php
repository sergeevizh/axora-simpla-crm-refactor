<?php

namespace App\Eloquent\Queries;

use App\Eloquent\Models\Banner;
use App\Repositories\IBannerDBRepository;

class EloquentBannerQueries implements IBannerDBRepository
{
    public function get(): array
    {
        $banners = Banner::where('visible', 1)->get();

        return $banners ? $banners->toArray() : [];
    }
}
