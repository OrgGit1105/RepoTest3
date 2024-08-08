<?php

namespace App\Providers;

use App\Repositories\Contracts\AnalyticRepositoryInterface;
use App\Repositories\Contracts\ArrivingReportRepositoryInterface;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\EmotionRepositoryInterface;
use App\Repositories\Contracts\GithubEvenRepositoryInterface;
use App\Repositories\Contracts\HistoryEditReportRepositoryInterface;
use App\Repositories\Contracts\ImageFaceRepositoryInterface;
use App\Repositories\Contracts\PolicyRepositoryInterface;
use App\Repositories\Contracts\RDSManagerRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\ScheduleRepositoryInterface;
use App\Repositories\Contracts\UploadFileRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\VIAMRDSRepositoryInterface;
use App\Repositories\Contracts\VIAMUserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Repository\ArrivingReportRepository;
use Repository\BaseRepository;
use Repository\AuthRepository;
use Laravel\Dusk\DuskServiceProvider;
use Repository\AnalyticRepository;
use Repository\EmotionRepository;
use Repository\GithubEvenRepository;
use Repository\HistoryEditReportRepository;
use Repository\ImageFaceRepository;
use Repository\PolicyRepository;
use Repository\RDSManagerRepository;
use Repository\RoleRepository;
use Repository\ScheduleRepository;
use Repository\UploadFileRepository;
use Repository\UserRepository;
use Repository\VIAMRDSRepository;
use Repository\VIAMUserRepository;

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
    $this->app->bind(AnalyticRepositoryInterface::class, AnalyticRepository::class);
    $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
    $this->app->bind(PolicyRepositoryInterface::class, PolicyRepository::class);
    $this->app->bind(VIAMUserRepositoryInterface::class, VIAMUserRepository::class);
    $this->app->bind(RDSManagerRepositoryInterface::class, RDSManagerRepository::class);
    $this->app->bind(VIAMRDSRepositoryInterface::class, VIAMRDSRepository::class);
    $this->app->bind(UploadFileRepositoryInterface::class, UploadFileRepository::class);
    $this->app->bind(GithubEvenRepositoryInterface::class, GithubEvenRepository::class);

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
