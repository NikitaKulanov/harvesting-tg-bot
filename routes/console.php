<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('set:webhook', function () {
    $action = $this->ask('What mode? [activation/shutdown]', 'activation');
    $this->comment('Start installing webhook...');
    $response = app(\App\Http\Controllers\TGController::class)->setWebhookBot(
        app(\App\Services\Telegram\RequestClient::class),
        $action
    );
    $this->info('Status: ' . $response->status() . '. Payload: ' . $response->content());
})->purpose('Set webhook for bot TG');
