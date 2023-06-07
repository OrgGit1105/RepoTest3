<?php


namespace Tests\Browser\Systems;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\DuskTestCase;

class GraphTest extends DuskTestCase
{
  public function test_can_view_preview_month_role_head_quater($browser)
  {
    $month = Carbon::now()->month;
    $year = Carbon::now()->year;
    $department = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', $month - 2)
      ->whereYear('risk_scores.month_year', '=', $year);
    $department = $department->groupBy('company_branchs.id', 'company_branchs.name')->limit(1)->get();
    $browser->pause(2000)->releaseMouse()
      ->pause(2000)
      ->press('#btn-prev')
      ->pause(5000)->assertSee($department[0]->name);
  }

  public function test_can_view_preview_month_role_department($browser)
  {
    $month = Carbon::now()->month;
    $year = Carbon::now()->year;
    $department = DB::table('company_branchs')
      ->select('company_branchs.id', 'company_branchs.id as position', 'company_branchs.name', DB::raw('count(*) as numberofemployee'))
      ->from('company_branchs')
      ->leftJoin('employees', 'company_branchs.id', '=', 'employees.company_branch')
      ->leftJoin('risk_scores', 'employees.employee_code', '=', 'risk_scores.employee_id')
      ->whereMonth('risk_scores.month_year', '=', $month - 2)
      ->whereYear('risk_scores.month_year', '=', $year)
      ->where('company_branchs.id', 2);
    $department = $department->groupBy('company_branchs.id', 'company_branchs.name')->limit(1)->get();
    $browser->pause(2000)->releaseMouse()
      ->pause(2000)
      ->press('#btn-prev')
      ->pause(5000)->assertSee($department[0]->name);
  }
}
