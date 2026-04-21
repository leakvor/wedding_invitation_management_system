<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('app:hello', function () {
    $this->comment('Wedding Invitation Management System');
})->purpose('Display a simple application greeting.');

