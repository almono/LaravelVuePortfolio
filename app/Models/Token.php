<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Token extends Model
{
    protected $table = 'tokens';

    public static function createToken($type = NULL, $userId = NULL) {
        if(!is_null($type) && !is_null($userId)) {
            $newToken = new Token();
            $newToken->token = md5($userId . now());
            $newToken->token_type = $type;
            $newToken->user_id = $userId;

            try {
                $newToken->save();
            } catch (\Exception $e) {
                dd($e->getMessage());
            }

            return $newToken;
        }

        return false;
    }

    public static function createRandomToken($type, $userId = NULL) {
        return md5($userId . $type . now());
    }
}
