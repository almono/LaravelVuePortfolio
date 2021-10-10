<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\Token;

class Request extends Model
{
    protected $table = 'requests';

    public function createRequest($type, $userId) {
        $req = new self();
        $req->request_type = $type;
        $req->request_user = $userId;
        $req->request_token = Token::createRandomToken($type);

        try {
            $req->save();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
