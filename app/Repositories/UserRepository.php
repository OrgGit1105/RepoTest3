<?php


namespace App\Repositories;


use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Repository\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
  protected $repository;

  public function __construct(Application $app)
  {
    parent::__construct($app);
  }

  public function model()
  {
    return User::class;
  }

  public function getCompanyBranchidbyUser($id)
  {
    return User::with('company_branchs')->find($id);
//        return $this->syncWithoutDetaching($id, 'company_branchs', 'name', 'address', 'description');
  }


  public function getCompanyBranchbyUser()
  {
    return User::with('company_branchs')->get();
//    return $this->model->with('company_branchs')->paginate($request->per_page);
//        return $this->syncWithoutDetaching($id, 'company_branchs', 'name', 'address', 'description');
  }


  public function getRoleidbyUser($id)
  {
    return User::with('roles')->find($id);
//        return $this->syncWithoutDetaching($id, 'roles', 'name', 'display_name', 'description');
  }


  public function getRolebyUser()
  {
    return User::with('roles')->get();
//    return $this->model->with('roles')->paginate($request->per_page);
//        return $this->syncWithoutDetaching($id, 'roles', 'name', 'display_name', 'description');
  }

  public function showAll(Request $request)
  {
    $users = User::with('roles')->with('company_branchs')->orderBy('created_at','desc');
    if (Auth::user() && Auth::user()->role_id != 1) {
      $users = $users->where('department_id', Auth::user()->department_id)->where('role_id', '!=', 1);
    }
    $users = $users->where('id', '<>', Auth::user()->id)->paginate($request->per_page);

    return $users;
  }

  public function create(array $attributes)
  {
    if (Auth::user()) {
      if (count($attributes) <= 3) {
        $attributes['role_id'] = Auth::user()->role_id;
        $attributes['department_id'] = Auth::user()->department_id;
      }
    }
    if (!isset($attributes['created_by']))
      $attributes['created_by'] = Auth::id();
    return $this->model()::create($attributes);
  }

  public function showId($id)
  {
    return User::with('roles')->with('company_branchs')->find($id);
  }

  public function update(array $attributes, $id)
  {
    if (!isset($attributes['updated_by']))
      $attributes['updated_by'] = Auth::id();
    return parent::update($attributes, $id);
  }

  public function delete($id)
  {
    if (!isset($attributes['deleted_by']))
      $attributes['deleted_by'] = Auth::id();
    return $this->model()::destroy($id);
  }
}
