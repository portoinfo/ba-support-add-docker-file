<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

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
})->describe('Display an inspiring quote');

Artisan::command('serve:custom', function () {
 
    // $this->call('route:cache', []);
    // $this->call('optimize', []);
    // Para usar os comandos acima -> remover rotas com action em clousure
    
    $this->call('view:clear', []);
    $this->call('route:clear', []);
    $this->call('config:cache', []);
    $this->call('config:clear', []);
    $this->call('cache:clear', []);
    $this->call('serve', []);

})->describe('Custom command to serve application and clear all the caches.');
