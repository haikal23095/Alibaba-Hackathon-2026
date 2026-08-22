<?php

/*
|--------------------------------------------------------------------------
| Firebase Web App Configuration
|--------------------------------------------------------------------------
|
| Konfigurasi Firebase SDK sisi browser (untuk Google Sign-In di halaman
| login/register). Nilai-nilai ini diambil dari Firebase Console:
| Project Settings -> Your apps -> Web app (SDK setup and configuration).
|
| Catatan: ini berbeda dengan FIREBASE_CREDENTIALS (service account JSON)
| yang dipakai oleh Admin SDK (kreait/laravel-firebase) di backend.
|
*/

$config = [
    'api_key' => env('FIREBASE_WEB_API_KEY'),
    'auth_domain' => env('FIREBASE_WEB_AUTH_DOMAIN'),
    'project_id' => env('FIREBASE_WEB_PROJECT_ID'),
    'storage_bucket' => env('FIREBASE_WEB_STORAGE_BUCKET'),
    'messaging_sender_id' => env('FIREBASE_WEB_MESSAGING_SENDER_ID'),
    'app_id' => env('FIREBASE_WEB_APP_ID'),
];

// True jika konfigurasi web app sudah terisi di .env
// (dihitung dari array agar tetap benar saat config di-cache)
$config['configured'] = filled($config['api_key'])
    && filled($config['auth_domain'])
    && filled($config['project_id'])
    && filled($config['app_id']);

return $config;
