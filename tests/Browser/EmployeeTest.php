<?php


namespace Tests\Browser;


use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Tests\DuskTestCase;

class EmployeeTest extends DuskTestCase
{
  public function testGeneral()
  {
    $this->browse(function ($browser) {
      $this->login();
      $this->test_can_view_employee_list($browser);
      $this->test_can_view_detail($browser);
      $this->test_list_eployee_sort($browser);
      $this->test_can_view_employee_see_emlpoyee_name_filter_all($browser);
      $this->test_can_view_emlpoyee_code_filter_data_one($browser);
      $this->logout($browser);
      $this->login2();
      $this->test_can_filter_role_deparment($browser);
    });
  }

  private function test_can_view_employee_list($browser)
  {
    $results = Employee::limit(3)->orderBy("employee_name");
    if($results) {
      $browser->visit('/employee/index')
        ->waitForText('社員リスト')
        ->releaseMouse()
        ->pause(5000)
        ->assertSee('社員リスト');
        foreach ($results as $item) {
          $browser->pause(1000)->assertSee($item->employee_name);
        }
    }
  }

  private function test_can_view_detail($browser)
  {
    $employee = Employee::join('risk_scores', 'employees.employee_code', 'risk_scores.employee_id')
      ->orderBy('risk_scores.retirement_score', 'desc');
    $employee = $employee->where(function ($query) {
      $now = Carbon::now();
      $date = Carbon::parse($now->year . '-' . $now->month . '-03 00:00:00');
      if ($date > $now) {
        $query->whereMonth('month_year', '=', $now->month - 2);
        $query->whereMonth('month_year', '=', $now->month - 2);
      } else {
        $query->whereMonth('month_year', '=', $now->month - 1);
      }
      $query->whereYear('month_year', '=', $now->year);
    });
    $employee = $employee->first();
    if ($employee) {
      $browser->visit('/employee/chart/' . $employee->employee_code)
        ->waitForText('退職予測スコア推移')
        ->releaseMouse()
        ->pause(5000)
        ->assertSee($employee->employee_name);
    }
  }

//  test sort see company branch
  private function test_list_eployee_sort($browser){
    $now = Carbon::now();
    $date = Carbon::parse($now->year . '-' . $now->month . '-03 00:00:00');
    $datasee = Employee::join('risk_scores', 'employees.employee_code', 'risk_scores.employee_id')->with(['company'])
      ->orderBy('risk_scores.retirement_score', 'desc');
    $datasee = $datasee->where(function ($query) use ($now, $date) {
      if ($date > $now) {
        $query->whereMonth('month_year', '=', $now->month - 2);
      } else {
        $query->whereMonth('month_year', '=', $now->month - 1);
      }
      $query->whereYear('month_year', '=', $now->year);
    });
    $datasee = $datasee->first();
    $now = Carbon::now();
    $date = Carbon::parse($now->year . '-' . $now->month . '-03 00:00:00');
    $datanotsee = Employee::join('risk_scores', 'employees.employee_code', 'risk_scores.employee_id')->with(['company'])
      ->orderBy('risk_scores.retirement_score', 'asc');
    $datanotsee = $datanotsee->where(function ($query) use ($now, $date) {
      if ($date > $now) {
        $query->whereMonth('month_year', '=', $now->month - 2);
      } else {
        $query->whereMonth('month_year', '=', $now->month - 1);
      }
      $query->whereYear('month_year', '=', $now->year);
    });
    $datanotsee = $datanotsee->first();
      if($datasee) {
        $browser->visit('/employee/index')
          ->waitForText('社員リスト')
          ->releaseMouse()
          ->pause(2000)
          // test list sort
          ->pause(3000)->click('.retirement_score')->pause(3000)
          ->assertSee($datasee->retirement_score)
          ->assertDontSee($datanotsee->retirement_score);
      }
  }

  private function test_can_view_employee_see_emlpoyee_name_filter_all($browser)
  {
    $results = Employee::limit(3)->orderBy("employee_name");
    if($results) {
      $browser
        ->waitForText('社員リスト')
        ->releaseMouse()
        ->pause(5000)
        ->click('.btn-apply')->pause(3000);
      foreach ($results as $item) {
        $browser->pause(1000)->assertSee($item->employee_name);
      }
    }
  }

  private function test_can_view_emlpoyee_code_filter_data_one($browser)
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
    $data = $data->limit(1)->get();
    if(count($data) > 1) {
      $browser
        ->waitForText('社員リスト')
        ->releaseMouse()
        ->pause(5000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->type('@employee_code', $data[0]['employee_code'])->pause(1000)
        ->click('.btn-apply')->pause(3000)
        ->assertSee($data[0]['employee_code'])
        ->assertDontSee($data[1]['employee_code']);
    }

  }

  private function logout($browser)
  {
    $browser->visit('/enrollment/index')
      ->waitForText('在籍予測')
      // test logout
      ->click('.btn-logout')->pause(5000)
      ->assertSee('ログイン');
  }

  private function test_can_filter_role_deparment($browser){
    $now = Carbon::now();
    $date = Carbon::parse($now->year . '-' . $now->month . '-03 00:00:00');
    $data = Employee::join('risk_scores', 'employees.employee_code', 'risk_scores.employee_id')->where('company_branch', 1)->with(['company'])
      ->orderBy('risk_scores.retirement_score', 'desc');

    $data = $data->where(function ($query) use ($now, $date) {
      if ($date > $now) {
        $query->whereMonth('month_year', '=', $now->month - 2);
      } else {
        $query->whereMonth('month_year', '=', $now->month - 1);
      }
      $query->whereYear('month_year', '=', $now->year);
    });
    $data = $data->limit(1)->get();
    if(count($data) > 1) {
      $browser->visit('/employee/index')
        ->waitForText('社員リスト')
        ->releaseMouse()
        ->pause(3000)
        ->click('#input-group-1 .custom-control-label')->pause(2000)
        ->click('.btn-apply')->pause(3000)
        ->assertSee($data[0]['retirement_score'])
        ->assertDontSee($data[1]['retirement_score']);
    }
  }
}
