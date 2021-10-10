<?php

namespace App\Models;

use Dompdf\Exception;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\Translation;

class TranslationSuggestion extends Model
{
    protected $table = 'translation_suggestions';

    public static function addNewSuggestion($translationId = null, $userId = null, $suggestion = null) {
        $result['status'] = 'error';

        if(!is_null($translationId) && !is_null($userId) && !is_null($suggestion)) {

            $existingSuggestions = self::where('user_id', $userId)->where('acceptance_status', '0')->get()->count();

            if($existingSuggestions >= 10) {
                $result['errorType'] = 'limitReached';
                return $result;
            }

            $sug = new self();
            $sug->translation_id = $translationId;
            $sug->user_id = $userId;
            $sug->suggestion = $suggestion;

            DB::beginTransaction();
            try {
                $sug->save();
                DB::commit();
                $result['status'] = 'success';
            } catch (\Exception $e) {
                $result['errorType'] = 'creationError';
                $result['error'] = [
                  'transId' => $translationId,
                  'userId' => $userId
                ];

                DB::rollBack();
            }
        }

        return $result;
    }

    public static function getUserTranslationSuggestions($userId, $status = null) {
        $userSuggestion = [];
        if(isset($userId) && !is_null($userId)) {
            $userSuggestion = self::where('user_id', $userId);

            if(!is_null($status)) {
                $userSuggestion = $userSuggestion->where('acceptance_status', $status);
            }

            $userSuggestion = $userSuggestion->orderBy('translation_id', 'DESC')->get()->toArray();

            if(isset($userSuggestion) && !is_null($userSuggestion)) {
                foreach($userSuggestion as $key => $value) {
                    $userSuggestion[$key]['translation_key'] = Translation::getTranslationKeyById($userSuggestion[$key]['translation_id']);
                }
            }

        }

        return $userSuggestion;
    }

    public static function checkIfSuggestionExists($userId, $suggestion, $lang) {
        return !is_null(self::where([
            ['user_id', $userId],
            ['acceptance_status', '0'],
            ['suggestion', $suggestion],
            ['language', $lang]
        ])->get()->first()) ? true : false;
    }

    public static function getUserTranslationSuggestionCount($userId, $status) {
        return self::where('user_id', $userId)->where('acceptance_status', $status)->get()->count();
    }

    public static function deleteTranslationSuggestion($userId, $suggestionId) {
        $result['status'] = 'error';
        if(self::where('user_id', $userId)->where('id', $suggestionId)->delete()) {
            $result['message'] = 'translationSuggestionDeleteSuccess';
            $result['status'] = 'success';
            return $result;
        }

        $result['message'] = 'translationSuggestionDeleteError';
        return $result;
    }

    public static function acceptUserSuggestion($suggestionId) {
        $suggestion = self::where('id', $suggestionId)->get()->first();

        if(!is_null($suggestion)) {
            $suggestion['acceptance_status'] = 1;

            DB::beginTransaction();
            try {
                $suggestion->save();
                DB::commit();
                return true;
            } catch (\Exception $e) {
                DB::rollBack();
                return false;
            }
        } else {
            return false;
        }
    }
}
