<?php
namespace Tests\Unit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Facade;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;
use Repository\AuthRepository;
use App\Http\Requests\LoginRequest;
class AuthTest extends TestCase
{
  protected $user;
  protected $param;
  public $authRepository;

  public function setUp() : void
  {
    parent::setUp();
    Facade::clearResolvedInstances();
    $this->faker = Faker::create();
    // chuẩn bị dữ liệu test
    $this->user = [
      'department_id '=> $this->faker->name,
      'name'=> $this->faker->name,
      'email'=> 'test@gmail.com',
      'phone'=> $this->faker->name,
      'password'=> '$2y$10$Zm/ejZK.gI8EEL/jr.tNQu0tP5qiNZZzrauKrPjlFhIu/6CMkPWeK',
      'user_name'=> $this->faker->name,
      'fax'=> $this->faker->name,
      'address'=> $this->faker->name,
      'avatar'=> $this->faker->name,
    ];
    $this->param = [
      'user_name'=> 'test@gmail.com',
      'password'=> '12345678',
    ];
    $this->request = new LoginRequest();
    $this->authRepository = new AuthRepository();
    $this->loginRequest  = new LoginRequest();
  }
  public function tearDown() : void
  {
    parent::tearDown();
  }
  /**
   * test Login.
   * @param LoginRequest $this->param
   * @param null $guard
   * @return void
   */
  public function testLogin()
  {
    // Gọi hàm tạo
    $request = new LoginRequest();
    $request->merge($this->param);
    $response=$this->authRepository->doLogin( $request, $guard = null);
    $this->assertTrue(true);
  }

  public function test_login_wrong_user_or_pass(){
    $param = [
      'user_name'=> 'test@gmail.com',
      'password'=> '1234567',
    ];
    $this->request->merge($param);
    $response=$this->authRepository->doLogin( $this->request, $guard = null);
    $this->assertEquals(false, $response['attempt']);
  }
  public function testLoginNotHavePassword()
  {
    $param = [
      'user_name'=> 'test@gmail.com',
      'password'=> '',
    ];
    $this->request->merge($param);
    $response=$this->authRepository->doLogin( $this->request, $guard = null);
    // dd($response);
    $this->assertEquals(false, $response['attempt']);
  }
  public function testLoginNotHaveParams()
  {
    $param = [
      'user_name'=> '',
      'password'=> '',
    ];
    $this->request->merge($param);
    $response=$this->authRepository->doLogin( $this->request, $guard = null);
    // dd($response);
    $this->assertEquals(false, $response['attempt']);
  }
  public function testLoginWrongTypeEmail()
  {
    $param = [
      'user_name'=> 'testgmail.com',
      'password'=> '12345678',
    ];
    $this->request->merge($param);
    $response=$this->authRepository->doLogin( $this->request, $guard = null);
    // dd($response);
    $this->assertEquals(false, $response['attempt']);
  }
  public function testLoginNotHaveEmailOrPhoneNumber()
  {
    $param = [
      'user_name'=> '',
      'password'=> '123456789',
    ];
    $this->request->merge($param);
    $response=$this->authRepository->doLogin( $this->request, $guard = null);
    // dd($response);
    $this->assertEquals(false, $response['attempt']);
  }

}
