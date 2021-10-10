<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class UserFollower extends Model
{
    protected $table = 'user_followers';
    public $timestamps = false;

    public static function getUserFollowers($userId) {
        return self::select('user_followers.*', 'users.username', 'users.avatar_path')->where('user_id', $userId)->join('users', 'users.id', '=', 'user_followers.follower_id')->get()->toArray();
    }

    public static function getUserFollows($userId) {
        return self::select('user_followers.*', 'users.username', 'users.avatar_path')->where('follower_id', $userId)->join('users', 'users.id', '=', 'user_followers.user_id')->get()->toArray();
    }

    public static function isUserFollowed($userId, $followerId) {
        $follow = self::where([
            ['user_id', $followerId],
            ['follower_id', $userId]
        ])->get()->first();

        if(isset($follow) && !is_null($follow)) {
            return true;
        }

        return false;
    }
}
