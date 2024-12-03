<?php

namespace App\Providers;

use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\TransactionDb;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryEloquentRepository::class
        );
        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreEloquentRepository::class
        );

        /**
         * DB Transaction
         */

        $this->app->bind(
            TransactionInterface::class,
            TransactionDb::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
