<?php
/**
 * Created by PhpStorm.
 * User: phuonglv
 * Year: 2021-07-26
 */

namespace Repository;

use App\Models\CompanyBranch;
use App\Models\ConfigRange;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\RiskScore;
use App\Repositories\Contracts\RetirementPredictionChartRepositoryI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Repository\BaseRepository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;

class RetirementPredictionChartRepository extends BaseRepository implements RetirementPredictionChartRepositoryI
{

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

  public function show($request){
    $max_month_year = RiskScore::select('month_year')->orderBy('month_year', 'DESC')->first();
    $min_month_year=RiskScore::select('month_year')->orderBy('month_year', 'ASC')->first();
    return ['max_month_year'=>$max_month_year->month_year,'min_month_year'=>$min_month_year->month_year];
  }

  public function detail($request)
  {
    $departments = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "{$request['month']}")
      ->whereYear('risk_scores.month_year', '=', "{$request['year']}");
    if (Auth::user() && Auth::user()->role_id != 1) {
      $departments = $departments->where('company_branchs.id', Auth::user()->department_id);
    }
    $departments = $departments->groupBy('company_branchs.id', 'company_branchs.name')->get()
      ->toArray();


    // result0 is the list of retirement prediction employees that risk_score equal and exceed the threshold value
    $departments_risk1 = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "{$request['month']}")
      ->whereYear('risk_scores.month_year', '=', "{$request['year']}")
      ->where('risk_scores.retirement_score', '>=', 0.007);
    if (Auth::user() && Auth::user()->role_id != 1) {
      $departments_risk = $departments_risk1->where('company_branchs.id', Auth::user()->department_id)->groupBy('company_branchs.id', 'company_branchs.name')->get()
      ->toArray();
    }
    else {
      $departments_risk2 = $departments_risk1->groupBy('company_branchs.id', 'company_branchs.name')->get()
        ->toArray();
      $departments_risks3_not_in=[];
      foreach ($departments_risk2 as $departments_risks) {
        $departments_risks3_not_in[] = $departments_risks->id;
      }

        $departments_risk3 = DB::table('company_branchs')
          ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name')
          ->from('company_branchs')
          ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
          ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
          ->whereMonth('risk_scores.month_year', '=', "{$request['month']}")
          ->whereYear('risk_scores.month_year', '=', "{$request['year']}")
          ->whereNotIn('company_branchs.id', $departments_risks3_not_in);

        $departments_risk3 = $departments_risk3->groupBy('company_branchs.id', 'company_branchs.name')->get()
          ->toArray();
        $departments_risk = array_merge($departments_risk2, $departments_risk3);
      }
//    }




//    $departments_risk1 = $departments_risk1->groupBy('company_branchs.id', 'company_branchs.name')->get()
//      ->toArray();
//    foreach($departments_risk1 as $key => $value){
//      if($value->numberofemployee <= 0) {
//        return 1;
//      }
//    }
//    $departments_risks2_not_in=[];
//    foreach ($departments_risk1 as $departments_risks1) {
//      $departments_risks2_not_in[] = $departments_risks1->id;
//    }
//
//    $departments_risk2 = DB::table('company_branchs')
//      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name')
//      ->from('company_branchs')
//      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
//      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
//      ->whereMonth('risk_scores.month_year', '=', "{$request['month']}")
//      ->whereYear('risk_scores.month_year', '=', "{$request['year']}")
//      ->whereNotIn('company_branchs.id', $departments_risks2_not_in);
//
//    if (Auth::user() && Auth::user()->role_id != 1) {
//      $departments_risk2 = $departments_risk2->where('company_branchs.id', Auth::user()->department_id);
//    }
//
//    $departments_risk2 = $departments_risk2->groupBy('company_branchs.id', 'company_branchs.name')->get()
//      ->toArray();
////      foreach ($departments_risk2 as $key => $value) {
////        if ($value->numberofemployee <= 0) {
////          return 1;
////        }
////      }
//
//    $departments_risk = array_merge($departments_risk1, $departments_risk2);


    $array = [];
    foreach ($departments as $department) {
      $risk_score = DB::table('company_branchs')
        ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('round( (select (count(*)*100)/' . $department->numberofemployee . ' from `company_branchs`
    left join `employees` on `company_branchs`.`id` = `employees`.`company_branch` left join `risk_scores` on `employees`.`employee_code` = `risk_scores`.`employee_id` where month(`risk_scores`.`month_year`) = "' . $request->month . '" and year(`risk_scores`.`month_year`) = "' . $request->year . '"
    and `company_branchs`.`name` = "' . $department->name . '"
    and `risk_scores`.`retirement_score` >= 0.007
    group by `company_branchs`.`id`, `company_branchs`.`name`), 1) as branchname_value'))
        ->from('company_branchs')
        ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
        ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
        ->whereMonth('risk_scores.month_year', '=', "{$request['month']}")
        ->whereYear('risk_scores.month_year', '=', "{$request['year']}")
        ->where('company_branchs.name', $department->name)
//        ->where('risk_scores.retirement_score', '>=', 0.007)
        ->groupBy('company_branchs.id', 'company_branchs.name')->get();
      if(count($risk_score)){
        array_push($array, $risk_score[0]);
//      }else{
//        return 2;
      }
    }
//    dd($array);
    return ['the_blue' => $departments_risk, 'the_orange' => $array];
  }
}

;


