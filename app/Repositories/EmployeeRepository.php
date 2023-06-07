<?php


namespace App\Repositories;


use App\Models\CompanyBranch;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\RiskScore;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\RiskScoreRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Repository\BaseRepository;
use Repository\RiskScoreRepository;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{

  protected $riskscoreRepository;

  public function __construct(Application $app, RiskScoreRepository $riskScoreRepository)
  {
    parent::__construct($app);
    $this->riskscoreRepository = $riskScoreRepository;
  }

  public function model()
  {
    // TODO: Implement model() method.
    return Employee::class;
  }

  public function getAll($request)
  {
    // $now = Carbon::now();
    // $date = Carbon::parse($now->year . '-' . $now->month . '-03 00:00:00');
    // $data = Employee::query()->leftJoin('risk_scores', function ($query) use ($now, $date){
    //   if ($date > $now) {
    //     $query->whereMonth('month_year', '=', $now->month - 1);
    //   } else {
    //     $query->whereMonth('month_year', '=', $now->month);
    //   }
    //   $query->whereYear('month_year', '=', $now->year)->on('employees.employee_code','=','risk_scores.employee_id');
    // });
    $riskScores = RiskScore::query()->orderBy('month_year', 'desc')->first();
    $data = Employee::query()->leftJoin('risk_scores', function ($query) use ($riskScores){
      $query->where('month_year', $riskScores->month_year)->on('employees.employee_code','=','risk_scores.employee_id');
    });
    if ($request->column_name && $request->sort == 'false') {
      $data = $data->orderBy($request->column_name,"ASC");
    } elseif ($request->column_name && $request->sort == 'true') {
      $data = $data ->orderBy($request->column_name,"DESC");
    }
    else{
      $data->orderBy('risk_scores.retirement_score_percent', 'desc');
    }
    if (Auth::user() && Auth::user()->role_id != 1) {
      $data = $data->where('company_branch', Auth::user()->department_id);
    }
    $data = $data->where(function ($query) use ($request) {
      if ($request->company_branch) {
        $query->where('company_branch', $request->company_branch);
      }
      if ($request->employee_code) {
        $query->where('employee_code', $request->employee_code);
      }
      if ($request->employee_name) {
        $query->where('employee_name', 'like', "%{$request->employee_name}%");
      }
      if ($request->threshold_value == 'true') {
        $query->where('retirement_score', '>=', "0.007");
      } elseif ($request->threshold_value == 'false') {
        $query->where('retirement_score', '<', "0.007");
      }
    })->with(['riskScore','company']);
    return ['employees' => $data->paginate($request->per_page)];
  }

  public function index($request)
  {
    $data = $this->model->with('company');
    if (Auth::user() && Auth::user()->role_id != 1 && Auth::user()->department_id) {
      $data = $data->where('company_branch', Auth::user()->department_id);
    }
    if ($request->column_name && $request->sort == 'true') {
      $data = $data->orderByRaw("{$request->column_name} ASC");
    } elseif ($request->column_name && $request->sort == 'false') {
      $data = $data->orderByRaw("{$request->column_name} DESC");
    }
    $employees = $data->paginate($request->per_page);
    return $employees;
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
        if (!$result['employee_code']) {
          return false;
        }
        $company_branch = CompanyBranch::query()->where('name', $result['company_branch'])->first();
        if (!$company_branch) {
          return 2;
        }
        $attributes = [
          'employee_code' => $result['employee_code'],
          'employee_name' => $result['employee_name'],
          'company_branch' => $company_branch->id,
        ];
        if (array_key_exists('joining_age_company', $result)) {
          $attributes ['joining_age_company'] = $result['joining_age_company'];
        }
        if (array_key_exists('date_joining_company', $result) && $result['date_joining_company']) {
          $datefrom = date("Y/m/d", strtotime($result['date_joining_company']));
          $attributes ['date_joining_company'] = $datefrom;
        }
        if (array_key_exists('spouse', $result)) {
          $attributes ['spouse'] = $result['spouse'];
        }
        if (array_key_exists('dependents', $result)) {
          $attributes ['dependents'] = $result['dependents'];
        }
        if (array_key_exists('worked_year', $result)) {
          $attributes ['worked_year'] = $result['worked_year'];
        }
        if (array_key_exists('shortest_service', $result)) {
          $attributes ['shortest_service'] = $result['shortest_service'];
        }
        if (array_key_exists('final_education', $result)) {
          $attributes ['final_education'] = $result['final_education'];
        }
        if (array_key_exists('total_worked', $result)) {
          $attributes ['total_worked'] = $result['total_worked'];
        }
        if (array_key_exists('date_out_company', $result) && $result['date_out_company']) {
          $dateto = date("Y/m/d", strtotime($result['date_out_company']));
          $attributes ['date_out_company'] = $dateto;
        }
        $this->insert($attributes);
      }
    }
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
          'employee_name' => $data[1],
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
          'employee_name' => $data[1]
        ]);
      }
    }
  }


  public function delete($id)
  {
    $attributes['deleted_by'] = Auth::id();
    return parent::delete($id);
  }

  public function getById($id)
  {
    return $this->model->find($id);
  }


  public function getByEmployeeId($request)
  {
    if (Auth::user() && Auth::user()->role_id != 1 && Auth::user()->department_id) {
      $this->model->where('company_branch_id', Auth::user()->department_id);
    }
    $now = Carbon::now()->year;
    $employee = $this->model->where('employee_code', '=', $request->employee_id)->first();
    if ($employee) {
      $result = RiskScore::with(['employees', 'employees.company']);
    }
    if (isset($request->year) && Carbon::parse($request->year . "/01/01")->year < $now) {
      $result = $result->whereYear('month_year', '=', $request->year);
    } else {
      $result = $result->whereBetween('month_year', [Carbon::now()->subMonths(12), Carbon::now()]);
    }
    $result = $result->where('employee_id', '=', $request->employee_id)->get();

    return $result;
  }

}
