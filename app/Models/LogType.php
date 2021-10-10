<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LogType extends Model
{
    protected $table = 'log_types';

    public static function getLogTypeName($typeId) {
        if(isset($typeId) && !is_null($typeId)) {
            return self::where('id', $typeId)->value('name');
        }
    }

    public static function getLogTypeId($typeName) {
        if(isset($typeName) && !is_null($typeName)) {
            return self::where('name', $typeName)->value('id');
        }
    }
}
