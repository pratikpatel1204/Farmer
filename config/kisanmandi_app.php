<?php

/*
|--------------------------------------------------------------------------
| KisanMandi mobile app settings  ->  config('kisanmandi_app.xxx')
|--------------------------------------------------------------------------
| .env me badlein, phir: php artisan config:clear
*/

return [

    // Isse purana app version ho to app "Please update" dikhayega
    'min_version' => [
        'android' => env('APP_MIN_VERSION_ANDROID', '1.0.0'),
        'ios'     => env('APP_MIN_VERSION_IOS', '1.0.0'),
    ],

    'store_url' => [
        'android' => env('APP_STORE_URL_ANDROID'),
        'ios'     => env('APP_STORE_URL_IOS'),
    ],

    'languages' => [
        ['code' => 'en', 'name' => 'English', 'short' => 'EN'],
        ['code' => 'hi', 'name' => 'हिन्दी', 'short' => 'HI'],
        ['code' => 'gu', 'name' => 'ગુજરાતી', 'short' => 'GU'],
        ['code' => 'mr', 'name' => 'मराठी', 'short' => 'MR'],
    ],

    // Website jaisa: pehli baar Hindi
    'default_language' => env('APP_DEFAULT_LANGUAGE', 'hi'),

    // Firebase service account JSON ka path (Firebase console > Project settings > Service accounts > Generate new private key).
    // File public/ me NAHI rakhni. Example: storage/app/firebase.json
    // Khali ho to push band rehta hai, baaki sab chalta hai.
    'fcm_credentials' => env('FCM_CREDENTIALS', 'storage/app/firebase.json'),

    // Android notification channel (app me isi naam se channel banana hai)
    'fcm_channel' => env('FCM_CHANNEL', 'kisanmandi_default'),

    // Contact Us form ke subject (website wale)
    'contact_subjects' => [
        'General question',
        'Selling my produce',
        'Buying / requirement',
        'Account or login help',
        'Report a problem',
    ],
];
