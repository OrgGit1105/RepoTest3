<?php

namespace App\Providers;


use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\EmotionRepositoryInterface;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use App\Repositories\Contracts\ImageFaceRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Repository\ArrivingReportRepository;
use Repository\BaseRepository;
use Repository\AuthRepository;
use Laravel\Dusk\DuskServiceProvider;
use Repository\EmotionRepository;
use Repository\HistoryEditReportRepository;
use Repository\ImageFaceRepository;
use Repository\RoleRepository;
use Repository\UserRepository;

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
    $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    $this->app->bind(UserRepositoryInterface::class,UserRepository::class);
    $this->app->bind(ArrivingReportRepositoryInterface::class,ArrivingReportRepository::class);
    $this->app->bind(HistoryEditReportRepositoryInterface::class,HistoryEditReportRepository::class);
    $this->app->bind(ImageFaceRepositoryInterface::class,ImageFaceRepository::class);
    $this->app->bind(EmotionRepositoryInterface::class,EmotionRepository::class);
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
