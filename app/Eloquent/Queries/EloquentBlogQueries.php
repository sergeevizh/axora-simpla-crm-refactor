<?php

namespace App\Eloquent\Queries;

use App\Eloquent\Models\Blog;
use App\Repositories\IBlogDBRepository;

class EloquentBlogQueries implements IBlogDBRepository
{
    public function get(): array
    {
        $posts = Blog::limit(3)->get();

        return $posts ? $posts->toArray() : [];
    }
}
