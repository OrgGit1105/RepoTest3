<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\RemindRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use App\Mail\RemindPasswordEmail;
use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Repository\AuthRepository;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @OA\Post(
     *   path="/api/auth/login",
     *   tags={"Auth"},
     *   summary="User Login",
     *   operationId="user_login",
     *     	@OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="file",
     *                      description="file",
     *                      type="file",
     *                   ),
     *                  @OA\Property(
     *                      property="email",
     *                      type = "string"
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type = "string"
     *                  ),
     *               ),
     *           ),
     *       ),
     *   @OA\Response(
     *     response=200,
     *     description="Gửi yêu cầu thành công",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"access_token":"Bearer ...","profile":{"id":1,"full_name":null,"email":"example@gmail.com","phone":null,"company":null,"address":null,"created_at":1570031021}}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Đăng nhập thất bại",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Sai tài khoản hoặc mật khẩu"}
     *     )
     *   ),
     *   security={},
     * )
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
  public function login(LoginRequest $request)
  {
    $loginResult= $this->authRepository->doLogin($request);
    if ($loginResult['attempt']) {
      $user = $loginResult['user'];
      return $this->responseJson(Response::HTTP_OK, [
        'access_token' => "Bearer " . $loginResult['attempt'],
        'profile' => new UserResource($user)
      ]);
    }
    return $this->responseJsonError(Response::HTTP_UNAUTHORIZED, isset($loginResult['msg']) ? $loginResult['msg'] : '', __('api.login.false'));
  }

    /**
     * @OA\Get(
     *   path="/api/profile",
     *   tags={"Auth"},
     *   summary="Get Profile",
     *   operationId="user_profile",
     *   @OA\Response(
     *     response=200,
     *     description="Gửi yêu cầu thành công",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":200,"data":{"id": 1, "name":"abc","email":"abc@gmail.com","phone":"0988737723","address":"Dia chi"}}
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Đăng nhập thất bại",
     *     @OA\MediaType(
     *      mediaType="application/json",
     *      example={"code":401,"message":"Sai tài khoản hoặc mật khẩu"}
     *     )
     *   ),
     *   security={{"auth": {}}},
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(){
        return $this->responseJson(200, auth()->user());
    }
  /**
   * @OA\Post(
   *   path="/api/auth/logout",
   *   tags={"Auth"},
   *   summary="logout",
   *   operationId="logout",
   *   @OA\Response(
   *     response=200,
   *     description="Logout success",
   *     @OA\MediaType(
   *      mediaType="application/json",
   *      example={"code":200,"message":"Logout success"}
   *     ),
   *   ),
   *   security={{"auth": {}}},
   * )
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout()
  {

    $this->authRepository->logout();
    return $this->responseJson(Response::HTTP_OK, null, trans('api.logout.success'));
  }

//  public function loginTest(Request $request)
//  {
//    $loginResult= $this->authRepository->doLoginTest($request);
//    if ($loginResult['attempt']) {
//      $user = $loginResult['user'];
//      return $this->responseJson(Response::HTTP_OK, [
//        'access_token' => "Bearer " . $loginResult['attempt'],
//        'profile' => new UserResource($user),
//        'checkUser' => auth('user')->user()
//      ]);
//    }
//    return $this->responseJsonError(Response::HTTP_UNAUTHORIZED, isset($loginResult['msg']) ? $loginResult['msg'] : '', __('api.login.false'));
//  }
}

