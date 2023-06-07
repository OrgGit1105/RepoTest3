<?php


namespace Tests\Browser;

use App\Models\CompanyBranch;
use App\Models\User;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;

class RetirementPredictionChartTest extends DuskTestCase
{

  /**
   * A basic browser test example.
   *
   * @throws \Throwable
   */

  public function testGeneral()
  {
    $this->browse(function ($browser) {
      $this->login4();
      $this->test_retirement_prediction_chart_list_department3($browser);
      $this->logout($browser);
      $this->login3();
      $this->test_retirement_prediction_chart_list_department2($browser);
      $this->logout($browser);
      $this->login2();
      $this->test_retirement_prediction_chart_list_department1($browser);
      $this->logout($browser);
      $this->login();
      $this->test_retirement_prediction_chart_list_headquater($browser);
    });
  }

  private function test_retirement_prediction_chart_list_department3($browser){
    // test list data department3
    $browser->visit('/retirement/index')
      ->waitForText('退職予測グラフ')
      ->pause(5000)->assertDontSee('YG茨城HC')
      ->pause(5000)->assertDontSee('管理本部');
    $month = Carbon::now()->month;
    $year = Carbon::now()->year;
    $departments = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "$month")
      ->whereYear('risk_scores.month_year', '=', "$year");
    $departments = $departments->groupBy('company_branchs.id', 'company_branchs.name')->get();

    $departments_risk = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name as branchname', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "$month")
      ->whereYear('risk_scores.month_year', '=', "$year")
      ->where('risk_scores.retirement_score', '>=', 0.4);
    $departments_risk = $departments_risk->groupBy('company_branchs.id', 'company_branchs.name')->get();

    $array = [];
    foreach ($departments as $department) {
      $risk_score = DB::table('company_branchs')
        ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name as branchname', DB::raw('(count(*)*100)/' . $department->numberofemployee . ', round( (select (count(*)*100)/' . $department->numberofemployee . ' from `company_branchs`
    left join `employees` on `company_branchs`.`id` = `employees`.`company_branch` left join `risk_scores` on `employees`.`employee_code` = `risk_scores`.`employee_id` where month(`risk_scores`.`month_year`) = "' . $month . '" and year(`risk_scores`.`month_year`) = "' . $year . '"
    and `company_branchs`.`name` = "' . $department->name . '"
    and `risk_scores`.`retirement_score` >= 0.4
    group by `company_branchs`.`id`, `company_branchs`.`name`), 0) as branchname_value'))
        ->from('company_branchs')
        ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
        ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
        ->whereMonth('risk_scores.month_year', '=', "$month")
        ->whereYear('risk_scores.month_year', '=', "$year")
        ->where('company_branchs.name', $department->name)
        ->where('risk_scores.retirement_score', '>=', 0.4)->groupBy('company_branchs.id', 'company_branchs.name')->get();
      array_push($array, $risk_score[0]);
    }
    $grapblue =$browser->elements('[seriesName=退職予測人数] > path');
    $graporange =$browser->elements('[seriesName=退職予測割合] > path');
    $grapblue[0]->click();
    sleep(5);
    // check data the number of employee department3
    $browser->assertSee('千葉HC')->pause(2000)->releaseMouse()->assertSee($departments_risk[2]->numberofemployee . ' 人');
    $graporange[0]->click();
    sleep(5);
    // check data the ratio department3
    $browser->assertSee('千葉HC')->pause(2000)->releaseMouse()->assertSee($array[2]->branchname_value . ' %');
  }

  private function test_retirement_prediction_chart_list_department2($browser){
    // test list data department2
    $browser->visit('/retirement/index')
      ->waitForText('退職予測グラフ')
      ->releaseMouse()
      ->pause(5000)->assertDontSee('管理本部')
      ->pause(5000)->assertDontSee('千葉HC');
    $month = Carbon::now()->month;
    $year = Carbon::now()->year;
    $departments = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "$month")
      ->whereYear('risk_scores.month_year', '=', "$year");
    $departments = $departments->groupBy('company_branchs.id', 'company_branchs.name')->get();

    $departments_risk = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name as branchname', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "$month")
      ->whereYear('risk_scores.month_year', '=', "$year")
      ->where('risk_scores.retirement_score', '>=', 0.4);
    $departments_risk = $departments_risk->groupBy('company_branchs.id', 'company_branchs.name')->get();

    $array = [];
    foreach ($departments as $department) {
      $risk_score = DB::table('company_branchs')
        ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name as branchname', DB::raw('(count(*)*100)/' . $department->numberofemployee . ', round( (select (count(*)*100)/' . $department->numberofemployee . ' from `company_branchs`
    left join `employees` on `company_branchs`.`id` = `employees`.`company_branch` left join `risk_scores` on `employees`.`employee_code` = `risk_scores`.`employee_id` where month(`risk_scores`.`month_year`) = "' . $month . '" and year(`risk_scores`.`month_year`) = "' . $year . '"
    and `company_branchs`.`name` = "' . $department->name . '"
    and `risk_scores`.`retirement_score` >= 0.4
    group by `company_branchs`.`id`, `company_branchs`.`name`), 0) as branchname_value'))
        ->from('company_branchs')
        ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
        ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
        ->whereMonth('risk_scores.month_year', '=', "$month")
        ->whereYear('risk_scores.month_year', '=', "$year")
        ->where('company_branchs.name', $department->name)
        ->where('risk_scores.retirement_score', '>=', 0.4)->groupBy('company_branchs.id', 'company_branchs.name')->get();
      array_push($array, $risk_score[0]);
    }
    $grapblue =$browser->elements('[seriesName=退職予測人数] > path');
    $graporange =$browser->elements('[seriesName=退職予測割合] > path');
    $grapblue[0]->click();
    sleep(5);
    // check data the number of employee department2
    $browser->assertSee('YG茨城HC')->pause(2000)->releaseMouse()->assertSee($departments_risk[1]->numberofemployee . ' 人');
    $graporange[0]->click();
    sleep(5);
    // check data the ratio department2
    $browser->assertSee('YG茨城HC')->pause(2000)->releaseMouse()->assertSee($array[1]->branchname_value . ' %');
  }

  private function test_retirement_prediction_chart_list_department1($browser){
    // test list data department1
    $browser->visit('/retirement/index')
      ->waitForText('退職予測グラフ')
      ->releaseMouse()
      ->pause(5000)->assertDontSee('YG茨城HC')
      ->pause(5000)->assertDontSee('千葉HC');
    $month = Carbon::now()->month;
    $year = Carbon::now()->year;
    $departments = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "$month")
      ->whereYear('risk_scores.month_year', '=', "$year");
    $departments = $departments->groupBy('company_branchs.id', 'company_branchs.name')->get();

    $departments_risk = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name as branchname', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "$month")
      ->whereYear('risk_scores.month_year', '=', "$year")
      ->where('risk_scores.retirement_score', '>=', 0.4);
    $departments_risk = $departments_risk->groupBy('company_branchs.id', 'company_branchs.name')->get();

    $array = [];
    foreach ($departments as $department) {
      $risk_score = DB::table('company_branchs')
        ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name as branchname', DB::raw('(count(*)*100)/' . $department->numberofemployee . ', round( (select (count(*)*100)/' . $department->numberofemployee . ' from `company_branchs`
    left join `employees` on `company_branchs`.`id` = `employees`.`company_branch` left join `risk_scores` on `employees`.`employee_code` = `risk_scores`.`employee_id` where month(`risk_scores`.`month_year`) = "' . $month . '" and year(`risk_scores`.`month_year`) = "' . $year . '"
    and `company_branchs`.`name` = "' . $department->name . '"
    and `risk_scores`.`retirement_score` >= 0.4
    group by `company_branchs`.`id`, `company_branchs`.`name`), 0) as branchname_value'))
        ->from('company_branchs')
        ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
        ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
        ->whereMonth('risk_scores.month_year', '=', "$month")
        ->whereYear('risk_scores.month_year', '=', "$year")
        ->where('company_branchs.name', $department->name)
        ->where('risk_scores.retirement_score', '>=', 0.4)->groupBy('company_branchs.id', 'company_branchs.name')->get();
      array_push($array, $risk_score[0]);
    }
    $grapblue =$browser->elements('[seriesName=退職予測人数] > path');
    $graporange =$browser->elements('[seriesName=退職予測割合] > path');
    $grapblue[0]->click();
    sleep(5);
    // check data the number of employee department1
    $browser->assertSee('管理本部')->pause(2000)->releaseMouse()->assertSee($departments_risk[0]->numberofemployee . ' 人');
    $graporange[0]->click();
    sleep(5);
    // check data the ratio department1
    $browser->assertSee('管理本部')->pause(2000)->releaseMouse()->assertSee($array[0]->branchname_value . ' %');
  }

  private function test_retirement_prediction_chart_list_headquater($browser)
  {
    // test list data
    $browser->visit('/retirement/index')
        ->waitForText('退職予測グラフ')
        ->pause(5000)->releaseMouse()
        ->pause(5000)
        ->press('#btn-prev')
        ->pause(15000)->assertSee('退職予測グラフ')
        ->releaseMouse()
        ->press('#btn-prev')
        ->pause(15000)->assertSee('退職予測グラフ')
        ->releaseMouse()
        ->press('#btn-prev')
        ->pause(15000)->assertSee('退職予測グラフ')
        ->releaseMouse()
        ->press('#btn-next')
        ->pause(15000)->assertSee('退職予測グラフ')
        ->releaseMouse()
        ->press('#btn-next')
        ->pause(15000)->assertSee('退職予測グラフ')
        ->releaseMouse()
        ->press('#btn-next')->assertSee('退職予測グラフ')
        ->pause(10000)
        ->waitForText('退職予測グラフ')
        ->pause(5000)->releaseMouse()->assertSee('退職予測グラフ')
        ->pause(5000)->releaseMouse()->assertSee('退職予測グラフ')
        ->assertSee('退職予測グラフ')
        ->pause(5000)->releaseMouse()
        ->pause(5000)->press('#chart .apexcharts-legend-marker')->assertSee('退職予測グラフ')
        ->pause(5000)->releaseMouse()
        ->pause(5000)->press('#chart .apexcharts-legend-marker')->assertSee('退職予測グラフ')
        ->pause(5000)->releaseMouse();
    $graphblue =$browser->elements('[seriesName=退職予測人数] > path');
    $graphorange =$browser->elements('[seriesName=退職予測割合] > path');
    $month = Carbon::now()->month;
    $year = Carbon::now()->year;
    $departments = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "$month")
      ->whereYear('risk_scores.month_year', '=', "$year");
    $departments = $departments->groupBy('company_branchs.id', 'company_branchs.name')->get();

    $departments_risk = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name as branchname', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', "$month")
      ->whereYear('risk_scores.month_year', '=', "$year")
      ->where('risk_scores.retirement_score', '>=', 0.4);
    $departments_risk = $departments_risk->groupBy('company_branchs.id', 'company_branchs.name')->get();

    $array = [];
    foreach ($departments as $department) {
      $risk_score = DB::table('company_branchs')
        ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name as branchname', DB::raw('(count(*)*100)/' . $department->numberofemployee . ', round( (select (count(*)*100)/' . $department->numberofemployee . ' from `company_branchs`
    left join `employees` on `company_branchs`.`id` = `employees`.`company_branch` left join `risk_scores` on `employees`.`employee_code` = `risk_scores`.`employee_id` where month(`risk_scores`.`month_year`) = "' . $month . '" and year(`risk_scores`.`month_year`) = "' . $year . '"
    and `company_branchs`.`name` = "' . $department->name . '"
    and `risk_scores`.`retirement_score` >= 0.4
    group by `company_branchs`.`id`, `company_branchs`.`name`), 0) as branchname_value'))
        ->from('company_branchs')
        ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
        ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
        ->whereMonth('risk_scores.month_year', '=', "$month")
        ->whereYear('risk_scores.month_year', '=', "$year")
        ->where('company_branchs.name', $department->name)
        ->where('risk_scores.retirement_score', '>=', 0.4)->groupBy('company_branchs.id', 'company_branchs.name')->get();
      array_push($array, $risk_score[0]);
    }
    // check the length of graph & the displayed number while hovering
    foreach ($graphblue as $key => $value) {
      $value->click();
      sleep(3);
      // check data the number of employee per department
      $browser->assertSee($departments_risk[$key]->branchname)->assertSee($departments_risk[$key]->numberofemployee . ' 人');
      if ($graphorange[$key]) {
        $graphorange[$key]->click();
        sleep(3);
        // check data the ratio per department
        $browser->assertSee($array[$key]->branchname)->assertSee($array[$key]->branchname_value . ' %');
      }
    }
  }

  private function logout($browser)
  {
    $browser->visit('/retirement/index')
      // test logout
      ->click('.btn-logout')->pause(5000)
      ->assertSee('ログイン');
  }
}



