<?php


namespace Tests\Browser\Systems;


use App\Models\DataManagement;
use App\Models\Employee;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\DuskTestCase;
use function GuzzleHttp\Psr7\str;

class HeaderTest extends DuskTestCase
{
  public function test_can_link_to_user_management($browser)
  {
    $users = User::query()->whereNull('deleted_at')->orderBy('created_at','desc')->limit(2)->get();
    $browser->releaseMouse()
      ->pause(2000)->mouseover('.link-setting')
      ->releaseMouse()->pause(2000)
      ->press('#link-user-management')->pause(5000);
    foreach ($users as $user) {
      $browser->pause(1000)->assertSee($user->email);
    }
  }

  public function test_can_link_to_impost_csv($browser)
  {
    $browser->pause(2000)->releaseMouse()
      ->pause(2000)->mouseover('.link-setting')
      ->releaseMouse()->pause(1000)
      ->press('#link-import')
      ->pause(5000)->assertSee('CSVインポート');
  }

  public function test_can_link_to_create_enrollment($browser)
  {
    $browser->pause(2000)->releaseMouse()
      ->pause(1000)->mouseover('.link-enrollment')
      ->releaseMouse()->pause(1000)
      ->press('#link-create-enrollment')
      ->pause(5000)->assertSee('在籍予測');
  }

  public function test_can_link_to_list_enrollment($browser)
  {
    $enrollments = Enrollment::where('is_accepted', Enrollment::ENROLLMENT_IS_ACCEPTED)->orderBy('id', 'desc')->limit(2)->get();
    $browser->releaseMouse()
      ->pause(2000)->mouseover('.link-enrollment')
      ->releaseMouse()->pause(1000)
      ->press('#link-list-enrollment')
      ->pause(6000)->releaseMouse();
    foreach ($enrollments as $enrollment) {
      $browser->pause(2000)->assertSee($enrollment->candidate_name);
    }
  }

  public function test_can_link_to_data_management($browser)
  {
    $data = Employee::limit(2)->get();
    $browser->releaseMouse()
      ->pause(2000)->mouseover('.link-enrollment')
      ->releaseMouse()->pause(1000)
      ->press('#link-data-management')
      ->pause(5000);
    foreach ($data as $item) {
      $browser->pause(1000)->assertSee($item->employee_name);
    }
  }

  public function test_can_link_to_user_management_by_role_department($browser)
  {
    $users = User::query()->whereNull('deleted_at')->where('department_id', 1)
      ->where('role_id', '!=', 1)->orderBy('created_at')->limit(2)->get();
    $browser->releaseMouse()
      ->pause(2000)->mouseover('.link-setting')
      ->releaseMouse()->pause(2000)
      ->press('#link-user-management')->pause(5000);
    foreach ($users as $user) {
      $browser->pause(1000)->assertSee($user->email);
    }
  }

  public function test_can_link_to_list_enrollment_by_role_department($browser)
  {
    $enrollments = Enrollment::where('is_accepted', Enrollment::ENROLLMENT_IS_ACCEPTED)->where('company_branch_id', 1)
      ->orderBy('id', 'desc')->limit(2)->get();
    $browser->releaseMouse()
      ->pause(2000)->mouseover('.link-enrollment')
      ->releaseMouse()->pause(1000)
      ->press('#link-list-enrollment')
      ->pause(6000)->releaseMouse();
    foreach ($enrollments as $enrollment) {
      $browser->pause(2000)->assertSee($enrollment->candidate_name);
    }
  }

  public function test_can_link_to_data_management_by_role_department($browser)
  {
    $data = Employee::where('company_branch', 1)->limit(2)->get();
    $browser->releaseMouse()
      ->pause(2000)->mouseover('.link-enrollment')
      ->releaseMouse()->pause(1000)
      ->press('#link-data-management')
      ->pause(5000);
    foreach ($data as $item) {
      $browser->pause(1000)->assertSee($item->employee_name);
    }
  }

  public function test_can_link_to_retirement_risk_prediction_graph_role_head_quater($browser)
  {
    $month=Carbon::now()->month;
    $year=Carbon::now()->year;
    $department = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', $month-1)
      ->whereYear('risk_scores.month_year', '=', $year);
    $department = $department->groupBy('company_branchs.id', 'company_branchs.name')->limit(1)->get();
    $browser->pause(2000)->releaseMouse()
      ->pause(2000)->mouseover('.link-retirement')
      ->releaseMouse()->pause(1000)
      ->press('#link-retirement')
      ->pause(5000)->assertSee($department[0]->name);
  }

  public function test_can_link_to_employee_all_role_quater($browser)
  {
    $now = Carbon::now();
    $date = Carbon::parse($now->year . '-' . $now->month . '-03 00:00:00');
    $data = Employee::join('risk_scores', 'employees.employee_code', 'risk_scores.employee_id')->with(['company'])
      ->orderBy('risk_scores.retirement_score', 'desc');
    $data = $data->where(function ($query) use ($now, $date) {
      if ($date > $now) {
        $query->whereMonth('month_year', '=', $now->month - 2);
      } else {
        $query->whereMonth('month_year', '=', $now->month - 1);
      }
      $query->whereYear('month_year', '=', $now->year);
    });
    $data = $data->limit(1)->first();
    if ($data) {
      $browser->pause(2000)->releaseMouse()
        ->pause(2000)->mouseover('.link-retirement')
        ->releaseMouse()->pause(1000)
        ->press('#link-employees')
        ->pause(5000)->assertSee($data->employee_name);
    } else {
      $browser->pause(2000)->releaseMouse()
        ->pause(2000)->mouseover('.link-retirement')
        ->releaseMouse()->pause(1000)
        ->press('#link-employees')
        ->pause(5000)->assertSee('社員リスト');
    }
  }

  public function test_can_link_to_digitaco_list($browser)
  {
    $browser->pause(2000)->releaseMouse()
      ->pause(2000)->mouseover('.link-retirement')
      ->releaseMouse()->pause(1000)
      ->press('#link-digitaco')
      ->pause(5000)->assertSee('データ管理');
  }

  public function test_can_link_to_employee_all_role_department($browser)
  {
    $now = Carbon::now();
    $date = Carbon::parse($now->year . '-' . $now->month . '-03 00:00:00');
    $data = Employee::join('risk_scores', 'employees.employee_code', 'risk_scores.employee_id')->with(['company'])
      ->orderBy('risk_scores.retirement_score', 'desc')->where('company_branch', 2);
    $data = $data->where(function ($query) use ($now, $date) {
      if ($date > $now) {
        $query->whereMonth('month_year', '=', $now->month - 2);
      } else {
        $query->whereMonth('month_year', '=', $now->month - 1);
      }
      $query->whereYear('month_year', '=', $now->year);
    });
    $data = $data->limit(1)->first();
    if ($data) {
      $browser->pause(2000)->releaseMouse()
        ->pause(2000)->mouseover('.link-retirement')
        ->releaseMouse()->pause(1000)
        ->press('#link-employees')
        ->pause(5000)->assertSee($data->employee_name);
    } else {
      $browser->pause(2000)->releaseMouse()
        ->pause(2000)->mouseover('.link-retirement')
        ->releaseMouse()->pause(1000)
        ->press('#link-employees')
        ->pause(5000)->assertSee('社員リスト');
    }
  }
  public function test_can_link_to_retirement_risk_prediction_graph_role_department($browser)
  {
    $month=Carbon::now()->month;
    $year=Carbon::now()->year;
    $department = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', $month-1)
      ->whereYear('risk_scores.month_year', '=', $year)
      ->where('company_branchs.id', 2);
    $department = $department->groupBy('company_branchs.id', 'company_branchs.name')->limit(1)->get();
    $browser->pause(2000)->releaseMouse()
      ->pause(2000)->mouseover('.link-retirement')
      ->releaseMouse()->pause(1000)
      ->press('#link-retirement')
      ->pause(5000)->assertSee($department[0]->name);
  }
}
