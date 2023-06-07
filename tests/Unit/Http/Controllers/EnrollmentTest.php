<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Requests\EnrollmentRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Enrollment;
use App\Repositories\Contracts\ConfigRangeRepositoryInterface;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Facade;
use Repository\ConfigRangeRepository;
use Repository\EnrollmentRepository;
use Tests\TestCase;
use Repository\AuthRepository;
use App\Http\Controllers\Api\MapdataController;
use Illuminate\Http\UploadedFile;
use Repository\MapdataRepository;
use App\Models\Import;
use App\Http\Requests\MapdataRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Contracts\MapdataRepositoryInterface;
use Mockery as m;
use Illuminate\Cache\Repository;
use Mockery\MockInterface;



class EnrollmentTest extends TestCase
{
  protected $enrollment;
  protected $param;
  protected $enrollmentRepository;
  protected $base;
  protected $urlLocalMasre;
  protected $urlActual;
  protected $urlMapdata;
  protected $request;
  protected $mapRequest;
  protected $file_test;
  protected $error_file;
  protected $controller;
  protected $configRangeController;
  protected $configRangeRepository;
  public $authRepository;
  /**
   * @var EnrollmentController
   */
  protected $enrollmentController;
  protected $enrollmentRequest;
  protected $repository;




  use WithFaker;

  public function setUp() : void
  {
    $app = new Application();
    $configRangeRepository = new ConfigRangeRepository($app);
    $enrollmentRepository = new EnrollmentRepository($app, $configRangeRepository);


    $this->afterApplicationCreated(function ()use($enrollmentRepository){
//      $this->configRangeRepository = m::mock($enrollmentRepository)->makePartial();
//      $this->repository = m::mock(EnrollmentRepository::class)->makePartial();
      $this->enrollmentRepository = m::mock($enrollmentRepository)->makePartial();
      $this->enrollmentController = new EnrollmentController(
        $this->app->instance(EnrollmentRepositoryInterface::class, $this->enrollmentRepository)
      );
    });
//    parent::setUp();
    //Facade::clearResolvedInstances();
//    $this->faker = Faker::create();
    $this->enrollmentRequest = new enrollmentRequest();
    // chuẩn bị dữ liệu test
    $this->enrollment = [
      'interview_date'=> '2020-10-10',
//      'interview_branch'=> 1,
      'candidate_name'=> 'hakata',
      'joining_age'=> 25,
      'spouse'=> 1,
      'dependents'=> 1,
      'worked_years'=> 5,
      'final_education'=> 5,
      'shortest_service'=> 1,
      'company_branch_id'=> 1,
//      'created_by'=>  Enrollment::first()->id,
      'created_by'=> 1,
      'updated_by'=> 1,
    ];
    $this->param = [
      'user_name'=> 'test@gmail.com',
      'password'=> '12345678',
    ];
//    $this->param = [
//      'candidate_name'=> 'hakata',
//      'joining_age'=> 25,
//    ];
    $this->authRepository = new AuthRepository();
    $this->loginRequest  = new LoginRequest();
    parent::setUp();
  }

  public function tearDown() : void
  {
    parent::tearDown();
  }

  /**
   * test Enrollment.
   * @param EnrollmentRequest $this ->param
   * @param null $guard
   * @return void
   * @throws \Exception
   */

  public function testEnrollmentCreate()
  {
//    $request = new EnrollmentRequest();
//    $request->merge($this->enrollment);
    $this->enrollmentRequest->merge($this->enrollment);
//    $data = json_decode (json_encode($data1), FALSE);
    $response = $this->enrollmentController->store($this->enrollmentRequest);
//    dd($response);
    $this->assertEquals(200,$response->getStatusCode());
  }


  public function testEnrollmentUpdate()
  {
//    $request = new EnrollmentRequest();
//    $request->merge($this->enrollment);
    $this->enrollmentRequest->merge($this->enrollment);
//    $this->enrollmentController->store($this->enrollmentRequest);
    $enrollment = Enrollment::factory()->create();
    $response = $this->enrollmentController->update($this->enrollmentRequest, $enrollment->id);
//    dd($response);
    $this->assertEquals(200,$response->getStatusCode());
  }


  public function testEnrollmentShowID()
  {
    $request = new LoginRequest();
    $request->merge($this->param);
    // $request->header()
    $this->authRepository->doLogin( $request, $guard = null);
//    $request = new EnrollmentRequest();
//    $request->merge($this->enrollment);
    $this->enrollmentRequest->merge($this->enrollment);
//    $this->enrollmentController->store($this->enrollmentRequest);
    $enrollment = Enrollment::factory()->create();
    $response = $this->enrollmentController->show($this->enrollmentRequest, $enrollment->id);
//    dd($response);
    $this->assertEquals(200,$response->getStatusCode());
  }

  public function testEnrollmentShowAll()
  {
//    $request = new EnrollmentRequest();
//      $request->merge($this->enrollment);
    $this->enrollmentRequest->merge($this->enrollment);
//    $enrollment = Enrollment::factory()->create();
    $this->enrollmentController->store($this->enrollmentRequest);
    $response = $this->enrollmentController->index($this->enrollmentRequest);
//    dd($response);
    $this->assertEquals(200,$response->getStatusCode());
  }

  public function testEnrollmentDestroy()
  {
//    $request = new EnrollmentRequest();
//    $request->merge($this->enrollment);
    $this->enrollmentRequest->merge($this->enrollment);
//    $this->enrollmentController->store($this->enrollmentRequest);
    $enrollment = Enrollment::factory()->create();
    $response = $this->enrollmentController->destroy($enrollment->id);
//    dd($response);
     $this->assertEquals(200,$response->getStatusCode());
  }
}

