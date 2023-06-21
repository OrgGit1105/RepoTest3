<?php

namespace Repository;

use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository implements AuthRepositoryInterface
{
    protected $shopRepository;

    public function __construct()
    {
    }

    /**
     *
     * Handle action login of user.
     *
     * @param LoginRequest $request
     * @param null $guard
     * @return array
     */
    public function doLogin($request, $guard = null): array
    {
      if (request()->has('email') && $request->email){
        $user=User::where('email',$request->email)->first();
        if (!$user){
          return [
            'attempt' => false,
            'msg' => trans('api.user.login.false')
          ];
        }
        $credentials['password'] = $request->password;
        $credentials['email'] = $request->email;
        $attempt = JWTAuth::attempt($credentials);
        if ($attempt){
          $user = User::where('email', $request->email)
            ->firstOrFail();
          $this->update(['jwt_active'=>$attempt],$user->id);
          return [
            'user' => $user,
            'attempt' => $attempt
          ];
        }
      }
      return [
        'attempt' => false,
        'msg' => trans('api.login.false')
      ];
    }

    /**
     * @param array $params
     * @return bool|void
     */
    public function register(array $params)
    {
        $user = User::create($params);
        $this->grantRoleNewUser($user);

        return $user;
    }

    protected function grantRoleNewUser(User &$user)
    {
        $roleOwnerDefault = array_key_first(config('laratrust_seeder.roles_structure', []));
        $shopOwner = Role::where('name', $roleOwnerDefault)->first();
        $user->attachRole($shopOwner);
    }


    public function update(array $attributes, $id)
    {
        $user = User::find($id);
         if($user->update($attributes)){
             return $user;
         }
         return [];
    }

  public function logout()
  {
    $user = auth()->user();
    if (!empty($user)) {
      $this->update(['jwt_active' => null], $user->id);
    }
    auth()->logout();
    return [];
  }

//  public function doLoginTest($request, $guard = null): array
//  {
//    if (request()->has('id')){
//      $user=User::find(request()->get('id'));
//      if (!$user){
//        return [
//          'attempt' => false,
//          'msg' => trans('api.user.login.false')
//        ];
//      }
//      $attempt = JWTAuth::fromUser($user);
//      if ($attempt){
////        $user = User::where('email', $request->email)
////          ->firstOrFail();
////        $this->update(['jwt_active'=>$attempt],$user->id);
//        return [
//          'user' => $user,
//          'attempt' => $attempt
//        ];
//      }
//    }
//    return [
//      'attempt' => false,
//      'msg' => trans('api.login.false')
//    ];
//  }
}
