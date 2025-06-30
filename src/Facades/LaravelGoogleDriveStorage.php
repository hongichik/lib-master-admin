<?php

namespace Hongdev\MasterAdmin\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hongdev\MasterAdmin\LaravelGoogleDriveStorage
 */
class LaravelGoogleDriveStorage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Hongdev\MasterAdmin\LaravelGoogleDriveStorage::class;
    }
}
