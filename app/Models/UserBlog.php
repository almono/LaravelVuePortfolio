<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class UserBlog extends Model
{
    protected $table = 'user_blogs';

    public static function getUserBlogs($params, $status = 1) {
        // 0 = not yet accepted
        // 1 = only active blogs
        // 2 = denied/rejected

        return self::title($params)
                    ->category($params)
                    ->author($params)
                    ->from($params)
                    ->to($params)
                    ->select('user_blogs.*', 'users.username', 'blog_categories.name as category_name')
                    ->join('blog_categories', 'user_blogs.category', '=', 'blog_categories.id')
                    ->join('users', 'users.id', '=', 'user_blogs.creator_id')->where([
                        ['status', '1'],
                        ['is_visible', '1']
                    ])->get();
    }

    public static function getUserAwaitingBlogsCount($userId) {
        return self::where([
            ['creator_id', $userId],
            ['status', '0']
        ])->get()->count();
    }

    public function scopeTitle($query, $params) {
        if(isset($params['title'])) {
            return $query->where('user_blogs.title', 'like', '%' . $params['title'] . '%');
        }

        return $query;
    }

    public function scopeCategory($query, $params) {
        if(isset($params['category'])) {
            return $query->where('user_blogs.category', $params['category']);
        }

        return $query;
    }

    public function scopeAuthor($query, $params) {
        if(isset($params['author'])) {
            return $query->where('users.username', 'like', '%' . $params['author'] . '%');
        }

        return $query;
    }

    public function scopeFrom($query, $params) {
        if(isset($params['date']) && isset($params['date']['start'])) {
            return $query->where('user_blogs.created_at', '>=', $params['date']['start']);
        }

        return $query;
    }

    public function scopeTo($query, $params) {
        if(isset($params['date']) && isset($params['date']['end'])) {
            return $query->where('user_blogs.created_at', '<=', $params['date']['end']);
        }

        return $query;
    }
}
