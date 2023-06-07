<?php

namespace App\Providers;


use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Repository\BaseRepository;
use Repository\AuthRepository;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
    $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
    //Customer
    if ($this->app->environment('local', 'testing')) {
      $this->app->register(DuskServiceProvider::class);
    }
//        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
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
