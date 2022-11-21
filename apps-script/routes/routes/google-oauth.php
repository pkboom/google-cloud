<?php

use App\Support\Client;
use Google\Service\Drive;
use Google\Service\Script;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// Set up callback route in Google API console and here.
Route::get('google-oauth', function () {
    $client = app(Client::class);
    $client->setRedirectUri('http://b999-76-70-89-251.ngrok.io/google-oauth/callback');
    $client->addScope([
        Script::DRIVE,
    ]);

    $auth_url = $client->createAuthUrl();

    return redirect()->to($auth_url);
});

Route::get('google-oauth/callback', function () {
    $client = app(Client::class);
    $client->setRedirectUri('http://b999-76-70-89-251.ngrok.io/google-oauth/callback');
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $access_token = $client->getAccessToken();

    dump($access_token);

    Cache::put('google_script_token', $access_token);

    return '<a href="http://google-script.test/google-oauth/drive">http://google-script.test/google-oauth/drive</a>';
});

Route::get('google-oauth/drive', function () {
    $client = new Client();
    $client->setAccessToken(Cache::get('google_script_token'));

    $drive = new Drive($client);

    $params = [
        'pageSize' => 5,
        'q' => '"root" in parents',
        'orderBy' => 'name',
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

    return '<a href="http://google-script.test/google-oauth/drive">http://google-script.test/google-oauth/drive</a>';
});
