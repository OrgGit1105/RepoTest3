<?php

namespace App\Providers;


use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Repositories\Contracts\CompanyBranchRepositoryInterface;
use App\Repositories\Contracts\ConfigRangeRepositoryInterface;
use App\Repositories\Contracts\DigitacoFileRepositoryI;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use App\Repositories\Contracts\GetMailRepositoryI;
use App\Repositories\Contracts\RetirementPredictionChartRepositoryI;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use App\Repositories\Contracts\DataManagementRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Repository\BaseRepository;
use Repository\AuthRepository;

use Repository\CompanyBranchRepository;
use Repository\ConfigRangeRepository;
use Repository\DigitacoFileRepository;
use Repository\GetMailRepository;
use Repository\RetirementPredictionChartRepository;
use Repository\RoleRepository;
use Laravel\Dusk\DuskServiceProvider;
use Repository\EnrollmentRepository;
use Repository\DataManagementRepository;
use Repository\RiskScoreRepository;

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
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    $this->app->bind(CompanyBranchRepositoryInterface::class, CompanyBranchRepository::class);
    $this->app->bind(EnrollmentRepositoryInterface::class, EnrollmentRepository::class);
    $this->app->bind(DataManagementRepositoryInterface::class, DataManagementRepository::class);
    $this->app->bind(ConfigRangeRepositoryInterface::class, ConfigRangeRepository::class);
    $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
    $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
    $this->app->bind(RetirementPredictionChartRepositoryI::class, RetirementPredictionChartRepository::class);
    $this->app->bind(GetMailRepositoryI::class, GetMailRepository::class);
    $this->app->bind(DigitacoFileRepositoryI::class, DigitacoFileRepository::class);
    $this->app->bind(RiskScoreRepository::class, RiskScoreRepository::class);
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
