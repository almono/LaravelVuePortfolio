<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ROUTES THAT DO NOT REQUIRE USER TO BE LOGGED IN TO BE USED/CALLED
Route::prefix('api')->group(function () {
    Route::post('/loginUser', 'App\Http\Controllers\UserController@loginUser')->name('user_login');
    Route::post('/authenticateUser', 'App\Http\Controllers\UserController@authenticateUser')->name('authenticate_user');
    Route::post('/logout', 'App\Http\Controllers\UserController@logout')->name('logout');
    Route::get('/getAvailableTranslations', 'App\Http\Controllers\TranslationController@getAvailableTranslations');
    Route::get('/getCVData', 'App\Http\Controllers\PersonalController@getCurriculumData');
    Route::post('/validateToken', 'App\Http\Controllers\UserController@validateToken')->name('validate_token');

    Route::post('/forgotPassword', 'App\Http\Controllers\UserController@forgotPassword');
    Route::get('/resetPassword/{userId}/{token}', 'App\Http\Controllers\UserController@resetPassword');

    // CRYPTO
    Route::post('/getCryptoNews', 'App\Http\Controllers\CryptoController@getCryptoNews');
    Route::post('/getCryptoPrice', 'App\Http\Controllers\CryptoController@getCryptoPrice');

});

Route::middleware('auth')->group(function () {
    Route::prefix('api')->group(function () {
        // USER ROUTES
        Route::get('/getUserProfile/{userId}', 'App\Http\Controllers\UserController@viewProfile');
        Route::post('/getUserDashboard', 'App\Http\Controllers\UserController@getUserDashboard');
        Route::post('/getDashboardActivities', 'App\Http\Controllers\UserController@getDashboardActivities');
        Route::post('/updateUserInfo', 'App\Http\Controllers\UserController@updateUserInfo');
        Route::post('/toggleAccountStatus', 'App\Http\Controllers\UserController@toggleAccountStatus');
        Route::post('/followUser', 'App\Http\Controllers\UserController@followUser');
        Route::post('/unfollowUser', 'App\Http\Controllers\UserController@unfollowUser');

        // BLOGS
        Route::post('/getUserBlogs', 'App\Http\Controllers\BlogController@getUserBlogs');
        Route::post('/createNewBlog', 'App\Http\Controllers\BlogController@createNewBlog');

        // OTHER ROUTES
        Route::post('/downloadCV', 'App\Http\Controllers\PersonalController@downloadCV');

        // TRANSLATIONS
        Route::post('/addNewTranslationKey', 'App\Http\Controllers\TranslationController@addNewTranslationKey');
        Route::post('/updateLanguageTranslations', 'App\Http\Controllers\TranslationController@updateLanguageTranslations');
        Route::post('/updateTranslationsData', 'App\Http\Controllers\TranslationController@updateTranslationsData');
        Route::post('/suggestNewTranslations', 'App\Http\Controllers\TranslationController@suggestNewTranslations');
        Route::post('/getUserSuggestions', 'App\Http\Controllers\TranslationController@getUserSuggestions');
        Route::post('/deleteTranslationSuggestion', 'App\Http\Controllers\TranslationController@deleteTranslationSuggestion');
        Route::post('/deleteTranslationKey', 'App\Http\Controllers\TranslationController@deleteTranslationKey');
    });
});

Route::get('/{any?}', App\Http\Controllers\MainController::class)->where('any', '.*');;