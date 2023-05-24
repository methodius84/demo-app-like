<?php

namespace App\Providers;

use App\Http\Controllers\Google\GoogleDocumentController;
use App\Http\Controllers\Google\GoogleDriveController;
use App\Http\Controllers\Google\GoogleSpreadsheetsController;
use \App\Services\Google\GoogleServiceInterface;
use App\Services\Google\GoogleSpreadsheetsService;
use App\Services\GooglePermissionService;
use App\Services\GoogleService;
use \App\Services\Google\GoogleDriveService;
use \Google\Service;
use Google_Service_Drive;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(GooglePermissionService::class)
            ->needs(GoogleServiceInterface::class)
            ->give(GoogleDriveService::class);
        $this->app->when(GoogleDriveService::class)
            ->needs(GoogleServiceInterface::class)
            ->give(GoogleDriveService::class);
        $this->app->when(GoogleSpreadsheetsController::class)
            ->needs(GoogleServiceInterface::class)
            ->give(GoogleSpreadsheetsService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(GoogleServiceInterface::class, GoogleDriveService::class);
    }
}
