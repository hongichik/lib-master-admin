<?php

use Illuminate\Support\Facades\Route;

Route::middleware('master-admin')->get('master-admin/test', function () {
    return view('master-admin::welcome');
});
