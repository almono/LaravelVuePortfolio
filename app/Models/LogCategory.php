<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LogCategory extends Model
{
    protected $table = 'log_categories';

    public static function getLogCategoryName($typeId) {
        if(isset($typeId) && !is_null($typeId)) {
            return self::where('id', $typeId)->value('name');
        }
    }

    public static function getLogCategoryId($typeName) {
        if(isset($typeName) && !is_null($typeName)) {
            return self::where('name', $typeName)->value('id');
        }
    }
}
