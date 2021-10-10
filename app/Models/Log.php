<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    protected $table = 'logs';

    public static function addLogEntry($logData) {
        if(isset($logData) && !is_null($logData)) {
            $newLog = new self();
            $newLog->admin_log = $logData['admin_log'];
            $newLog->user_id = $logData['user_id'];
            $newLog->log_category = $logData['log_category'];
            $newLog->log_type = $logData['log_type'];
            $newLog->log_info = isset($logData['log_info']) && !is_null($logData['log_info']) ? $logData['log_info'] : 'noLogInfo';

            try {
                $newLog->save();
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }
    }

    public function logCategory() {
        return $this->hasOne(LogCategory::class);
    }

    public static function getUserLastLogin($userId) {
        $logType = LogCategory::getLogCategoryId('login_success');
        return self::where('user_id', $userId)->where('log_category', $logType)->orderBy('created_at', 'DESC')->first()->value('created_at');
    }
}
