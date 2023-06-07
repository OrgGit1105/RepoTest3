<?php
/**
 * Created by PhpStorm.
 * User: cuongnt
 * Year: 2021-06-23
 */

namespace Repository;

use App\Http\Requests\DataManagementRequest;
use App\Jobs\DataMagementJob;
use App\Models\CompanyBranch;
use App\Models\DataManagement;
use App\Models\User;
use App\Repositories\Contracts\DataManagementRepositoryInterface;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DataManagementRepository extends BaseRepository implements DataManagementRepositoryInterface
{

  public function __construct(Application $app)
  {
    parent::__construct($app);

  }

  /**
   * Instantiate model
   *
   * @param DataManagement $model
   */

  public function model()
  {
    return DataManagement::class;
  }

  public function index($request)
  {
    if (Auth::user() && Auth::user()->role_id!=1 && Auth::user()->department_id) {
      $data = $this->model->with('company')->where('company_branch', Auth::user()->department_id)->paginate($request->per_page);
    } else {
      $data = $this->model->with('company')->paginate($request->per_page);
    }
    return $data;
  }

  /** @noinspection PhpUndefinedMethodInspection */
  public function create(array $attributes)
  {
    $this::truncate();
    foreach ($attributes as $result) {
      if ($result) {
        $dataManagement = $this->model->where('employee_code', 'like', '%' . $result['employee_code'] . '%')->first();
        if ($result['employee_code'] == '') {
          return 1;
        }
        if ($dataManagement) {
          return $dataManagement;
        }
//        if (!preg_match("/^[0-9]{4}-[0-9]{4}-[0-9]{4}$/", $result['employee_code'])) {
//          return false;
//        }
        if (!$result['employee_code']) {
          return false;
        }
        $company_branch = CompanyBranch::query()->where('name', $result['company_branch'])->first();
        if (!$company_branch) {
          return 2;
        }
        $datefrom = date("Y/m/d", strtotime($result['date_joining_company']));
        $attributes = [
          'employee_code' => $result['employee_code'],
          'employee_name' => $result['employee_name'],
          'joining_age_company' => $result['joining_age_company'],
          'date_joining_company' => $datefrom,
          'spouse' => $result['spouse'],
          'dependents' => $result['dependents'],
          'worked_year' => $result['worked_year'],
          'final_education' => $result['final_education'],
          'shortest_service' => $result['shortest_service'],
          'total_worked' => $result['total_worked'],
          'company_branch' => $company_branch->id,
//          'company_branch' => $result['company_branch'],
        ];
        if($result['date_out_company']){
          $dateto = date("Y/m/d", strtotime($result['date_out_company']));
          $attributes ['date_out_company']=$dateto;
        }
        $this->insert($attributes);
      }}
    return true;
  }


  public function import(array $attributes)
  {
    $this->readFile($attributes['data_file']);
    return true;
  }


  public function readFile($file)
  {
    $file = fopen($file, "r");
    $list = array();
    while (($data = fgetcsv($file, 200, ",")) !== FALSE) {
      $list[] = $data;
    }
    foreach ($list as $key => $data) {
      if ($key) {
        if (!preg_match("/^[0-9]{4}-[0-9]{4}-[0-9]{4}$/", $data[0])) {
          return true;
        }
        $datefrom = date("Y/m/d", strtotime($data[3]));
        $dateto = date("Y/m/d", strtotime($data[4]));
        $attributes = [
          'employee_code' => $data[0],
          'staffs_name' => $data[1],
          'joining_age_company' => $data[2],
          'date_joining_company' => $datefrom,
          'date_out_company' => $dateto,
          'spouse' => $data[5],
          'dependents' => $data[6],
          'worked_year' => $data[7],
          'final_education' => $data[8],
          'shortest_service' => $data[9],
          'total_worked' => $data[10],
          'company_branch' => $data[11],
        ];
        parent::create($attributes);
      }
    }
  }


  public function createtest(array $attributes)
  {
    $model = $this->model->create($attributes);
    return $model;
  }

  public function updateFile($file)
  {
    $file = fopen($file, "r");
    $all_data = array();
    $list = array();
    while (($data = fgetcsv($file, 200, ",")) !== FALSE) {
      $list[] = $data;
    }
    foreach ($list as $key => $data) {
      $data = $data->find($data[0]);
// if product exists and the value also exists
      if ($data and $data[1]) {
        $data->update([
          'staffs_name' => $data[1]
        ]);
      }
    }
  }


  public function delete($id)
  {
    $attributes['deleted_by'] = Auth::id();
    return parent::delete($id);
  }

}
