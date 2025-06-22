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

Artisan::command('debug:filesystem', function () {
    $disk = \Illuminate\Support\Facades\Storage::disk('media');
    logger()->info('[Debug FS]', [
        'root' => $disk->path('/'),
        'existe' => file_exists($disk->path('/')),
        'escribible' => is_writable($disk->path('/')),
        'archivos' => scandir($disk->path('/')),
    ]);
    $this->info('Revisar logs: storage/logs/laravel.log');
});
