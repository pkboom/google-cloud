<?php

use Google\Client;
use Google\Service\Drive;
use Google\Service\Script;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

// We get a user token from Google using Socialite to manage their Google Drive.
// Set up callback route in Google API console and .env after running `valet share`.
Route::get('manage-user-drive', function () {
    return Socialite::driver('google')
        ->scopes([
            Script::DRIVE,
        ])
        ->redirect();
});

Route::get('manage-user-drive/callback', function () {
    $user = Socialite::driver('google')->stateless()->user();

    Cache::put('google_script_token', $user->token);

    dump($user);

    return '<a href="http://google-script.test/manage-user-drive/drive">http://google-script.test/manage-user-drive/drive</a>';
});

Route::get('manage-user-drive/drive', function () {
    $client = new Client();
    $client->setAccessToken(Cache::get('google_script_token'));
    $client->addScope([
        Script::DRIVE,
    ]);

    $drive = new Drive($client);

    // https://developers.google.com/drive/api/v3/reference/files/list
    // https://developers.google.com/drive/api/guides/search-files
    $params = [
        'pageSize' => 5,
        // 'q' => 'mimeType = "application/vnd.google-apps.folder" and "root" in parents',
        'q' => '"root" in parents',
        'orderBy' => 'name',
        // 'pageToken' => 'nextPageToken',
    ];

    $results = $drive->files->listFiles($params);
    dump($results);

    if (empty($results)) {
        dump('no files');
    } else {
        foreach ($results as $file) {
            dump([$file->getId(), $file->getName(), $file->getMimeType()]);
        }
    }

    return '<a href="http://google-script.test/manage-user-drive/drive">http://google-script.test/manage-user-drive/drive</a>';
});
