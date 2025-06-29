<?php

use Illuminate\Support\Facades\Route;

Route::middleware('master-admin')->get('master-admin', function () {
    return view('master-admin::welcome');
});
