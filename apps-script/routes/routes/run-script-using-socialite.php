<?php

use Google\Client;
use Google\Service\Script;
use Google\Service\Script\ExecutionRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

// We get a user token from Google using Socialite to run google scripts.
// Set up callback route in Google API console and .env after running `valet share`.
Route::get('run-script-using-socialite', function () {
    return Socialite::driver('google')
        ->scopes([
            Script::DRIVE,
            // Script::DOCUMENTS,
            // Script::SPREADSHEETS,
            // Script::SCRIPT_PROJECTS,
            // Script::SCRIPT_DEPLOYMENTS,
            // Script::SCRIPT_PROCESSES,
        ])
        ->redirect();
});

Route::get('run-script-using-socialite/callback', function () {
    $user = Socialite::driver('google')->stateless()->user();

    Cache::put('google_script_token', $user->token);

    dump($user);

    return '<a href="http://google-script.test/run-script-using-socialite/drive">http://google-script.test/run-script-using-socialite/drive</a>';
});

Route::get('run-script-using-socialite/drive', function () {
    $client = new Client();
    $client->setAccessToken(Cache::get('google_script_token'));

    $script = new Script($client);
    $request = new ExecutionRequest();
    $request->setFunction('execute'); // The name of the Apps Script function to call

    // Deployment ID
    // $deploymentId = 'AKfycbwoYlLS6mWGvPibhAS6wVcUcGihY9udenZImnysAjHGJyfruwebzMs6OdroCry0zjvR';
    $headDeploymentId = 'AKfycbyiOPMsu7M38_tEyCpcUoJmrM7yGPBNeM9iGNQz9fU';

    $response = $script->scripts->run($headDeploymentId, $request);

    dump($response);

    return '<a href="http://google-script.test/run-script-using-socialite/drive">http://google-script.test/run-script-using-socialite/drive</a>';
});
