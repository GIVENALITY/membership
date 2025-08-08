<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/users', function () {
    return view('users.index');
})->name('users.index');

Route::get('/settings', function () {
    return view('settings.index');
})->name('settings.index');

Route::get('/settings/profile', function () {
    return view('settings.profile');
})->name('settings.profile');

Route::get('/settings/account', function () {
    return view('settings.account');
})->name('settings.account');
