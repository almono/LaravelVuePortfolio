<?php

namespace App\Http\Controllers;

use App\Models\TranslationSuggestion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\DB;

use App\Models\Log;
use App\Models\LogCategory;
use App\Models\LogType;
use App\Models\Translation;

class TranslationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAvailableTranslations(Request $request) {
        $translations = Translation::getAllTranslations();

        foreach($translations as $trans) {
            $result['translations']['EN'][$trans['wordKey']] = $trans['EN'];
            $result['translations']['PL'][$trans['wordKey']] = $trans['PL'];
            $result['translations']['JP'][$trans['wordKey']] = $trans['JP'];
        }

        $status = '400';
        if(!is_null($result['translations'])) {
            $status = '200';
        }

        return response()->json($result, $status);
    }

    public function addNewTranslationKey(Request $request) {
        $data = $request->all();
        $result['status'] = 'error';

        if(!isset($data['translationKey']) || !isset($data['translationValue'])) {
            $result['message'] = 'translationAddError';
            return response($result);
        }

        if(!isset($data['translationLanguage']) || !in_array($data['translationLanguage'], ['EN', 'PL', 'JP'])) {
            $result['message'] = 'translationLanguageSelectError';
            return response($result);
        }

        $updatedTranslation = Translation::addOrUpdateTranslationByKey($data['translationKey'], $data['translationLanguage'], $data['translationValue']);

        if(isset($updatedTranslation['status']) && !$updatedTranslation['status']) {
            $result['message'] = isset($updatedTranslation['message']) ? $updatedTranslation['message'] : 'translationKeyUpdateError';
            return response($result);
        }

        $result['status'] = 'success';
        $result['message'] = 'translationKeyCreationSuccess';
        return response($result);
    }

    public function updateLanguageTranslations(Request $request) {
        $data = $request->all();
        $result['status'] = 'error';

        if(!isset($data['translations']) || !isset($data['translations'])) {
            $result['message'] = 'translationsUpdateError';
            return response($result);
        }

        if(!isset($data['language']) || !in_array($data['language'], ['EN', 'PL', 'JP'])) {
            $result['message'] = 'translationLanguageSelectError';
            return response($result);
        }

        foreach($data['translations'] as $transKey => $transValue) {
            $updatedTranslation = Translation::addOrUpdateTranslationByKey($transKey, $data['language'], $transValue);
            if(isset($updatedTranslation['status']) && $updatedTranslation['status'] == 'error') {
                $result['message'] = 'translationUpdateFail';
                return response($result);
            }
        }

        $result['status'] = 'success';
        $result['message'] = 'translationsUpdateSuccess';
        return response($result);
    }

    public function updateTranslationsData(Request $request) {
        $data = $request->all();
        $result['status'] = 'error';

        if(!isset($data['language'])) {
            return response($result);
        }

        if(!isset($data['language']) || !in_array($data['language'], ['EN', 'PL', 'JP'])) {
            $result['message'] = 'translationLanguageSelectError';
            return response($result);
        }

        $result['status'] = 'success';
        $translations = Translation::getLangTranslations($data['language']);
        foreach($translations as $trans) {
            $result['translations'][$trans['wordKey']] = $trans[$data['language']];
        }

        return response($result);
    }

    public function suggestNewTranslations(Request $request) {
        $data = $request->all();
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];
        $result['status'] = 'error';
        $suggestionsPresent = false;

        if(!isset($data['language']) || is_null($data['language'])) {
            $result['message'] = 'translationLanguageSelectError';
            return response($result);
        }

        if(!isset($data['translations']) || is_null($data['translations'])) {
            $result['message'] = 'missingTranslationsError';
            return response($result);
        }

        if(isset($userData) && !is_null($userData)) {
            foreach($data['translations'] as $key => $trans) {
                if(TranslationSuggestion::getUserTranslationSuggestionCount($userData->id, '0') >= 10) {
                    $result['message'] = 'translationSuggestionLimitReached';
                    return response($result);
                }

                $translationId = Translation::getTranslationIdByKey($key);

                if(!TranslationSuggestion::checkIfSuggestionExists($userData->id, $trans, $data['language'])) {
                    DB::beginTransaction();
                    $suggestion = new TranslationSuggestion();
                    $suggestion->translation_id = $translationId;
                    $suggestion->user_id = $userData->id;
                    $suggestion->suggestion = $trans;
                    $suggestion->language = $data['language'];
                    $suggestion->suggestion_comment = 'Awaiting Approval';

                    try {
                        $suggestion->save();
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $result['message'] = 'suggestionCreationError';
                        dd($e->getMessage());
                    }
                } else {
                    $suggestionsPresent = true;
                }
            }
        } else {
            $result['message'] = 'userNotPermitted';
            return response($result);
        }

        $result['status'] = 'success';
        if($suggestionsPresent) {
            $result['message'] = 'translationSuggestionSuccessSkippingDuplicates';
        } else {
            $result['message'] = 'translationSuggestionSuccess';
        }

        return response($result);
    }

    public function getUserSuggestions(Request $request) {
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];

        $result['suggestions'] = TranslationSuggestion::getUserTranslationSuggestions($userData->id);
        return response($result);
    }

    public function deleteTranslationSuggestion(Request $request) {
        $data = $request->all();
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];
        $result['status'] = 'error';

        if(!isset($data['suggestionId']) || is_null($data['suggestionId'])) {
            $result['message'] = 'translationSuggestionNotProvided';
        }

        $suggestion = TranslationSuggestion::where('id', $data['suggestionId'])->get()->first();
        $suggestionInfo = '';

        if(!is_null($suggestion)) {
            $suggestionInfo = 'Suggestion removed: ' . $suggestion['suggestion'];
        }

        $result = TranslationSuggestion::deleteTranslationSuggestion($userData->id, $data['suggestionId']);

        if($result['status'] == 'success') {
            $categoryId = LogCategory::getLogCategoryId('translation_suggestion_removed');
            $typeId = LogType::getLogTypeId('info');

            Log::addLogEntry([
                'admin_log' => 0,
                'user_id' => $userData->id,
                'log_category' => $categoryId,
                'log_type' => $typeId,
                'log_info' => $suggestionInfo
            ]);
        }

        return response($result);
    }

    public function deleteTranslationKey(Request $request) {
        $data = $request->all();
        $token = \JWTAuth::getToken();
        $tokenData = \JWTAuth::getPayload($token);
        $userData = $tokenData['user'];
        $result['status'] = 'error';

        if(!isset($data['translationKey']) || is_null($data['translationKey'])) {
            $result['message'] = 'translationKeyNotProvided';
        }

        if(!isset($data['translationLanguage']) || is_null($data['translationLanguage'])) {
            $result['message'] = 'translationLanguageNotProvided';
        }

        $result = Translation::deleteTranslationKey($data['translationKey'], $data['translationLanguage']);

        if($result['status'] == 'success') {
            $categoryId = LogCategory::getLogCategoryId('translation_removed');
            $typeId = LogType::getLogTypeId('info');
            $result['message'] = 'translationRemovedSuccess';

            Log::addLogEntry([
                'admin_log' => 1,
                'user_id' => $userData->id,
                'log_category' => $categoryId,
                'log_type' => $typeId,
                'log_info' => 'Translation removed for key: ' . $data['translationKey'] . ' for ' . $data['translationLanguage']
            ]);
        }

        return response($result);
    }
}
