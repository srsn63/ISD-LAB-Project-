<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', function () {
    return view('login_dashboard');
})->name('login.dashboard');

Route::get('/signup', function () {
    return view('signup');
})->name('signup');
