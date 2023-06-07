<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-22
 */

namespace Repository;

use App\Models\CompanyBranch;
use App\Repositories\Contracts\CompanyBranchRepositoryInterface;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class CompanyBranchRepository extends BaseRepository implements CompanyBranchRepositoryInterface
{
  protected $model;

  public function __construct(Application $app)
  {
    parent::__construct($app);

  }

  /**
   * Instantiate model
   *
   * @param CompanyBranch $model
   */

  public function model()
  {
    return CompanyBranch::class;
  }

  public function getByRole()
  {
    return $this->model->where('role_id', 2)->get();
  }

  public function getByUser()
  {
    // TODO: Implement getByUser() method.
    if (Auth::user() && Auth::user()->department_id) {
      return $this->model->where('id', Auth::user()->department_id)->first();
    }
  }

  public function orderByRole()
  {
    // TODO: Implement orderByRole() method.
    return $this->model->orderBy('id');
  }
}
