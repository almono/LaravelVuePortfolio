<?php

namespace App\Models;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Translation extends Model
{
    protected $table = 'translations';
    protected $fillable = ['wordKey', 'EN', 'PL', 'JP'];

    public static function getAllTranslations() {
        return self::get()->toArray();
    }

    public static function getLangTranslations($lang) {
        if(Schema::hasColumn('translations', $lang)) {
            return self::select('wordKey', $lang)->get()->toArray();
        } else {
            return [];
        }
    }

    public static function getTranslationsByKey($wordKey) {
        return self::where('wordKey', $wordKey)->get()->toArray();
    }

    public static function getLangTranslatedWord($lang, $wordKey) {
        if(Schema::hasColumn('translations', $lang)) {
            return self::where('wordKey', $wordKey)->get($lang)->orderBy($lang, 'ASC')->toArray();
        } else {
            return [];
        }
    }

    public static function getTranslationIdByKey($transKey) {
        return self::where('wordKey', $transKey)->value('id');
    }

    public static function getTranslationKeyById($transId) {
        return self::where('id', $transId)->value('wordKey');
    }

    public static function checkIfTranslationExists($wordKey) {
        return self::where('wordKey', $wordKey)->first();
    }

    public static function addOrUpdateTranslationByKey($wordKey, $language, $translation) {
       try {
           DB::beginTransaction();
           $translation = self::updateOrCreate(
               ['wordKey' => trim($wordKey)],
               [$language => trim($translation)]
           );
           DB::commit();
       } catch (\Exception $e) {
           $result['status'] = false;
           $result['message'] = $e->getMessage();
           return $result;
       }

       $result['status'] = 'success';
       return $result;
    }

    public static function deleteTranslationKey($wordKey, $language) {
        try {
            DB::beginTransaction();

            $translation = self::where('wordKey', $wordKey)->get()->first();
            if(!is_null($translation)) {
                $translation->$language = '';
                $translation->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
            return $result;
        }

        $result['status'] = 'success';
        return $result;
    }
}
