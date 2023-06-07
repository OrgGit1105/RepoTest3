<?php


namespace Tests\Browser\Systems;


use App\Models\Employee;
use Carbon\Carbon;
use Tests\DuskTestCase;

class EmployeeTest extends DuskTestCase
{
  public function test_can_filter_employee_list_by_company_branch($browser)
  {
    $browser->pause(1000)
      ->releaseMouse()->pause(1000)->click('#input-group-1 .custom-control-label')->pause(1000)
      ->select('@company', 10)->pause(1000)->releaseMouse()
      ->press('.btn-apply')
      ->pause(5000)->assertSee('社員リスト');
  }

  public function test_can_filter_employee_list_by_employee_code($browser)
  {
    $browser->pause(1000)
      ->releaseMouse()->pause(1000)->click('#input-group-2 .custom-control-label')->pause(1000)
      ->type('@employee_code', '1111-2222-0003')->pause(1000)->releaseMouse()
      ->press('.btn-apply')
      ->pause(5000)->assertSee('社員リスト');
  }

  public function test_can_filter_employee_list_by_employee_name($browser)
  {
    $browser->pause(1000)
      ->releaseMouse()->pause(1000)->click('#input-group-3 .custom-control-label')->pause(1000)
      ->type('@employee_name', 'nguyen')->pause(1000)->releaseMouse()
      ->press('.btn-apply')
      ->pause(5000)->assertSee('社員リスト');
  }

  public function test_can_sort_employee_by_risk_score($browser)
  {
    $browser->pause(1000)
      ->releaseMouse()->pause(1000)
      ->click('.retirement_score')->pause(2000)->click('.retirement_score')
      ->pause(5000)->assertSee('社員リスト');
  }

  public function test_can_link_to_employee_detail($browser,$employee)
  {
    $browser->pause(1000)
      ->releaseMouse()->pause(1000)->click('#input-group-1 .custom-control-label')->pause(1000)
      ->pause(1000)
      ->releaseMouse()->pause(1000)->click('#input-group-2 .custom-control-label')->pause(1000)
      ->pause(1000)
      ->releaseMouse()->pause(1000)->click('#input-group-3 .custom-control-label')->pause(1000)
      ->pause(1000)->releaseMouse()
      ->press('.btn-apply')
      ->pause(2000)->releaseMouse()
      ->pause(2000)
      ->press('#btn-employee-' . $employee->employee_code)
      ->pause(5000)
      ->waitForText('退職予測スコア推移')
      ->releaseMouse()
      ->pause(5000)
      ->assertSee($employee->employee_name);
  }

  public function test_can_view_preview_year_of_employee_detail($browser, $employee)
  {
    $browser->pause(2000)->releaseMouse()
      ->pause(2000)
      ->press('.btn-select')
      ->pause(5000)
      ->waitForText('退職予測スコア推移')
      ->releaseMouse()
      ->pause(5000)
      ->assertSee($employee->employee_name);
  }
}
