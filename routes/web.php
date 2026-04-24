<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Baris sakti: Kalau ada yang akses /admin/dashboard, lempar ke /admin
Route::redirect('/admin/dashboard', '/admin');

Route::get('/login', \App\Livewire\Auth\UnifiedLogin::class)->name('login');