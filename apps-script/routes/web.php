<?php

use Illuminate\Support\Facades\Route;

foreach (glob(__DIR__.'/routes/*.php') as $filename) {
    require $filename;
}

Route::get('/', function () {
    $content = '<a href="http://google-script.test/manage-user-drive">http://google-script.test/manage-user-drive</a><br><br>';
    $content .= '<a href="http://google-script.test/google-oauth">http://google-script.test/google-oauth</a><br><br>';
    $content .= '<a href="http://google-script.test/run-script-using-socialite">http://google-script.test/run-script-using-socialite</a><br><br>';
    $content .= '<a href="http://google-script.test/manipulate-docs">http://google-script.test/manipulate-docs</a><br><br>';
    $content .= 'Let\'s do something fun!';

    return $content;
});
