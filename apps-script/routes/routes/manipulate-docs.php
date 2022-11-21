<?php

use App\Support\Client;
use Google\Service\Script;
use Google\Service\Script\ExecutionRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

// Set up callback route in Google API console and here.
// The created file has `restricted` access.
// Created pdf file can't be opened in iframe. So the only option is to download it or redirect to it.
// Pdf files will remain so we need another function to remove them.
Route::get('manipulate-docs', function () {
    $client = app(Client::class);
    $client->setRedirectUri('http://1c80-76-70-89-251.ngrok.io/manipulate-docs/callback');
    $client->addScope([
        Script::DRIVE,
        // https://developers.google.com/apps-script/reference/url-fetch/url-fetch-app
        'https://www.googleapis.com/auth/script.external_request', // This is required for using UrlFetchApp.
    ]);

    $auth_url = $client->createAuthUrl();

    return redirect()->to($auth_url);
});

Route::get('manipulate-docs/callback', function () {
    $client = app(Client::class);
    $client->setRedirectUri('http://1c80-76-70-89-251.ngrok.io/manipulate-docs/callback');
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $access_token = $client->getAccessToken();

    dump($access_token);

    Cache::put('google_script_token', $access_token);

    return '<a href="http://google-script.test/manipulate-docs/drive">http://google-script.test/manipulate-docs/drive</a>';
});

Route::get('manipulate-docs/drive', function () {
    $client = app(Client::class);
    $client->setAccessToken(Cache::get('google_script_token'));

    $service = new Script($client);
    $request = new ExecutionRequest();
    $request->setFunction('execute'); // The name of the Apps Script function to call
    $request->setParameters([
        [
            'find' => '{{cover_client}}',
            'replace' => 'foo',
        ],
        [
            'find' => '{{address}}',
            'replace' => 'bar',
        ],
    ]);

    // Head Deployment ID is only for testing.
    $headDeploymentId = 'AKfycbxgyHV1j6yH8-fQrw0e0xEYHXgX-04X-oTcDi0W9w';

    $response = $service->scripts->run($headDeploymentId, $request);

    dump($response);
    dump($response->response['result']);

    return '<a href="http://google-script.test/manipulate-docs/drive">http://google-script.test/manipulate-docs/drive</a>';
});

Route::view('welcome', 'welcome');
