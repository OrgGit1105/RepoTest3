<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\Api\UserController;
use App\Http\Requests\UserRequest;
use App\Models\Enrollment;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Faker\Factory as Faker;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Repository\EnrollmentRepository;
use Tests\TestCase;
use Repository\AuthRepository;
use App\Repositories\UserRepository;
use Mockery as m;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Cache\Repository;
use Mockery\MockInterface;
use Illuminate\Http\Request;



class UserTest extends TestCase
{
  protected $user;
  protected $userRepository;
  /**
   * @var UserController
   */
  protected $userController;
  protected $repository;
  protected $userRequest;
  // create false
  protected $user_create_email_in_validate;
  protected $user_create_username_not_null;
  protected $user_create_password_not_null;
  protected $user_create_email_not_null;
  protected $user_create_email_username_password_not_null;
  // update false
  protected $user_update_email_in_validate;
  protected $user_update_email_not_null;
  protected $user_update_username_not_null;
  protected $user_update_username_email_not_null;


  use WithFaker;

  public function setUp() : void
  {
    $app = new Application();
    $userRepository = new UserRepository($app);

    $this->afterApplicationCreated(function ()use($userRepository){
      $this->userRepository = m::mock($userRepository)->makePartial();
      $this->userController = new UserController(
        $this->app->instance(UserRepositoryInterface::class, $this->userRepository)
      );
    });
    $this->userRequest = new UserRequest();
    $this->faker = Faker::create();

    // chuẩn bị dữ liệu test
    $this->user = [
      'username'=> 'test25',
      'email'=> 'ngoson919597@gmail.com',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '$2y$10$WNkRsh5.82Igk/r.MIsmHeUo6i2A7E4KsN54f02NTIEIMTdcBOGhO',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_create_email_in_validate = [
      'username'=> 'test25',
      'email'=> 'ngoson919597gmail.com',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '$2y$10$WNkRsh5.82Igk/r.MIsmHeUo6i2A7E4KsN54f02NTIEIMTdcBOGhO',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_create_username_not_null = [
      'username'=> '',
      'email'=> 'ngoson919597@gmail.com',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '$2y$10$WNkRsh5.82Igk/r.MIsmHeUo6i2A7E4KsN54f02NTIEIMTdcBOGhO',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_create_password_not_null = [
      'username'=> 'ngoson919597',
      'email'=> 'ngoson919597@gmail.com',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_create_email_not_null = [
      'username'=> 'ngoson91959',
      'email'=> '',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_create_email_username_password_not_null = [
      'username'=> '',
      'email'=> '',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_update_email_in_validate = [
      'username'=> 'test25',
      'email'=> 'ngoson919597gmail.com',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '$2y$10$WNkRsh5.82Igk/r.MIsmHeUo6i2A7E4KsN54f02NTIEIMTdcBOGhO',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_update_email_not_null = [
      'username'=> 'test25',
      'email'=> '',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '$2y$10$WNkRsh5.82Igk/r.MIsmHeUo6i2A7E4KsN54f02NTIEIMTdcBOGhO',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_update_username_not_null = [
      'username'=> '',
      'email'=> 'test@gmail.com',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '$2y$10$WNkRsh5.82Igk/r.MIsmHeUo6i2A7E4KsN54f02NTIEIMTdcBOGhO',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    $this->user_update_username_email_not_null = [
      'username'=> '',
      'email'=> '',
      'phone'=> '0392999601',
      'name'=> 'son',
      'password'=> '$2y$10$WNkRsh5.82Igk/r.MIsmHeUo6i2A7E4KsN54f02NTIEIMTdcBOGhO',
      'fax'=> '01293123',
      'address'=> 'hanoi',
      'gender'=> 1,
      'status'=> 4,
    ];
    parent::setUp();

  }

  public function tearDown() : void
  {
    parent::tearDown();
  }
  /**
   * test User.
   * @param UserRequest $this->param
   * @param null $guard
   * @return void
   */

//  test OK

  public function testUserCreateSuccess()
  {
    $this->userRequest->merge($this->user);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }

  public function testUserUpdateSuccess()
  {
    // Gọi hàm tạo
    $this->userRequest->merge($this->user);
    $user = User::factory()->create();
    $response = $this->userController->update($this->userRequest, $user->id);
    $this->assertEquals(200,$response->getStatusCode());
  }

//  Test OK
  public function testUserShowID()
  {
    // Gọi hàm tạo
    $this->userRequest->merge($this->user);
    $user = User::factory()->create();
    $response = $this->userController->show($user->id);
    $this->assertEquals(200,$response->getStatusCode());
  }
////    Test OK
  public function testUserShowAll()
  {
    $this->userRequest->merge($this->user);
    User::factory()->create();
    $response = $this->userController->index($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }


//// Test OK
  public function testUserDestroy()
  {
    $this->userRequest->merge($this->user);
    $user = User::factory()->create();
    $response = $this->userController->destroy($user->id);
    $this->assertEquals(200,$response->getStatusCode());
  }


  // case false
  // create email in validate
  public function testUserNotCreateEmailInValidate()
  {
    $this->userRequest->merge($this->user_create_email_in_validate);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }
  // create User name not null
  public function testUserNotCreateUserNameNotNull()
  {
    $this->userRequest->merge($this->user_create_username_not_null);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }

  // create password not null
  public function testUserNotCreatePasswordNotNull()
  {
    $this->userRequest->merge($this->user_create_password_not_null);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }
  // create email not null
  public function testUserNotCreateEmailNotNull()
  {
    $this->userRequest->merge($this->user_create_email_not_null);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }
  // create email username and password not null
  public function testUserNotCreateEmailUsernamePasswordNotNull()
  {
    $this->userRequest->merge($this->user_create_email_username_password_not_null);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }
  // update user email email invalidate
  public function testUserNotUpdateEmailInValidate()
  {
    $this->userRequest->merge($this->user_update_email_in_validate);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }
  // update user email not null
  public function testUserNotUpdateEmailNotNull()
  {
    $this->userRequest->merge($this->user_update_email_not_null);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }
  // user update username not null
  public function testUserNotUpdateUsernameNotNull()
  {
    $this->userRequest->merge($this->user_update_username_not_null);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }
  // user update username and email not null
  public function testUserNotUpdateUsernameEmailNotNull()
  {
    $this->userRequest->merge($this->user_update_username_email_not_null);
    $response = $this->userController->store($this->userRequest);
    $this->assertEquals(200,$response->getStatusCode());
  }
}
