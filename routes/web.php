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

Route::get('/logout', function () {
    return redirect('/');
})->name('logout');

// Members Routes
Route::get('/members', function () {
    return view('members.index');
})->name('members.index');

Route::get('/members/create', function () {
    return view('members.create');
})->name('members.create');

Route::get('/members/search', function () {
    return view('members.search');
})->name('members.search');

// Cashier Routes
Route::get('/cashier', function () {
    return view('cashier.index');
})->name('cashier.index');

// Dining Routes
Route::get('/dining', function () {
    return view('dining.index');
})->name('dining.index');

// Discounts Routes
Route::get('/discounts', function () {
    return view('discounts.index');
})->name('discounts.index');

// Reports Routes
Route::get('/reports/members', function () {
    return view('reports.members');
})->name('reports.members');

Route::get('/reports/dining', function () {
    return view('reports.dining');
})->name('reports.dining');

Route::get('/reports/discounts', function () {
    return view('reports.discounts');
})->name('reports.discounts');

// Notifications Routes
Route::get('/notifications', function () {
    return view('notifications.index');
})->name('notifications.index');
