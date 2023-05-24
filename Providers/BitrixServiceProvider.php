<?php

namespace App\Providers;

use App\Http\Controllers\Bitrix\DepartmentController;
use App\Http\Controllers\Bitrix\TaskController;
use App\Http\Controllers\Bitrix\UserController;
use App\Services\Bitrix\ServiceInterface;
use App\Services\Bitrix\DepartmentService;
use App\Services\Bitrix\TaskService;
use App\Services\Bitrix\UserService;
use Illuminate\Support\ServiceProvider;

class BitrixServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(UserController::class)
        ->needs(ServiceInterface::class)
        ->give(UserService::class);

        $this->app->when(DepartmentController::class)
            ->needs(ServiceInterface::class)
            ->give(DepartmentService::class);

        $this->app->when(TaskController::class)
            ->needs(ServiceInterface::class)
            ->give(TaskService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
