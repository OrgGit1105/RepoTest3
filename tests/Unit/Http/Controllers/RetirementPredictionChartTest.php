<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Api\RetirementPredictionChartController;
use App\Http\Requests\DataManagementRequest;
use App\Http\Requests\RetirementPredictionChartRequest;
use App\Http\Requests\UserRequest;
use App\Models\DataManagement;
use App\Models\DigitacoFile;
use App\Models\User;
use App\Repositories\Contracts\RetirementPredictionChartRepositoryI;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Facade;
use Repository\RetirementPredictionChartRepository;
use Repository\EnrollmentRepository;
use Tests\TestCase;
use Repository\AuthRepository;
use App\Repositories\UserRepository;
use Mockery as m;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Cache\Repository;
use Mockery\MockInterface;



class RetirementPredictionChartTest extends TestCase
{
  protected $retirementPredictionChart;
  protected $retirementPredictionChartRipository;
  /**
   * @var RetirementPredictionChartController
   */
  protected $retirementPredictionChartController;
  protected $repository;
  protected $retirementPredictionChartRequest;

  use WithFaker;

  public function setUp() : void
  {
    $app = new Application();
    $retirementPredictionChartRipository = new RetirementPredictionChartRepository($app);

    $this->afterApplicationCreated(function ()use($retirementPredictionChartRipository){
      $this->retirementPredictionChartRipository = m::mock($retirementPredictionChartRipository)->makePartial();
      $this->retirementPredictionChartController = new RetirementPredictionChartController(
        $this->app->instance(RetirementPredictionChartRepositoryI::class, $this->retirementPredictionChartRipository )
      );
    });
    $this->retirementPredictionChartRequest = new RetirementPredictionChartRequest();
    parent::setUp();

  }

  public function tearDown() : void
  {
    parent::tearDown();
  }
  /**
   * test User.
   * @param RetirementPredictionChartRequest $this->param
   * @param null $guard
   * @return void
   */


////    Test OK
  public function testRetirementPredictionChartList()
  {
    $response = $this->retirementPredictionChartController->index($this->retirementPredictionChartRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }

}
