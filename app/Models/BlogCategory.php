<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class BlogCategory extends Model
{
    protected $table = 'blog_categories';

    public static function getBlogCategories() {
        return self::all();
    }
}
