<?php

use Illuminate\Support\Facades\Route;

Route::view('/admin', 'admin')->name('admin');

Route::redirect('/', '/admin');
