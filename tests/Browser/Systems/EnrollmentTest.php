<?php
//
//
//namespace Tests\Browser\Systems;
//
//
//use App\Models\Employee;
//use Tests\DuskTestCase;
//
//class EnrollmentTest extends DuskTestCase
//{
//  public function test_can_create_enrollment($browser)
//  {
//    $data = Employee::limit(2)->get();
//    $browser->select('@company_branch_id', 1)->pause(1000)
//      ->type('@candidate_name', str_shuffle('new name'))->pause(1000)
//      ->type('@joining_age', 30)->pause(1000)
//      ->select('@spouse', 0)->pause(1000)
//      ->select('@dependents', 2)->pause(1000)
//      ->select('@worked_years', 3)->pause(1000)
//      ->select('@final_education', 2)->pause(1000)
//      ->type('@shortest_service', 24)->pause(1000)
//      ->releaseMouse()->pause(2000)
//      ->press('.btn-simulation')->pause(5000)->assertSee('在籍予測結果');
//    foreach ($data as $item) {
//      $browser->pause(1000)->assertSee($item->employee_name);
//    }
//  }
//
//  public function test_can_filter_result_by_company_branch($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)->click('#input-group-1 .custom-control-label')->pause(1000)
//      ->select('@company', '1')->pause(1000)->releaseMouse()
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('類似社員データ');
//  }
//
//  public function test_can_filter_result_by_user_code($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)
//      ->click('#input-group-1 .custom-control-label')->pause(1000)
//      ->click('#input-group-2 .custom-control-label')->pause(1000)
//      ->type('@userCode', '1111-2222-0002')->pause(1000)
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('類似社員データ');
//  }
//
//  public function test_can_filter_result_by_user_name($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)
//      ->click('#input-group-2 .custom-control-label')->pause(1000)
//      ->click('#input-group-3 .custom-control-label')->pause(1000)
//      ->type('@userName', 'nguyen')->pause(1000)
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('類似社員データ');
//  }
//
//  public function test_can_filter_list_enrollment_by_date_interview($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)->click('#input-group-1 .custom-control-label')->pause(1000)
//      ->pause(1000)->click('#startDate')
//      ->pause(1000)->click('.rounded-circle')
//      ->pause(1000)->click('#endDate')
//      ->pause(1000)->click('.rounded-circle')
//      ->pause(1000)->releaseMouse()
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('在籍予測リスト');
//  }
//
//  public function test_can_filter_list_enrollment_by_interview_location($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)
//      ->click('#input-group-1 .custom-control-label')->pause(1000)
//      ->click('#input-group-2 .custom-control-label')->pause(2000)->select('.custom-select', 2)
//      ->pause(1000)->releaseMouse()
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('在籍予測リスト');
//  }
//
//  public function test_can_filter_list_enrollment_by_candidate_name($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)
//      ->click('#input-group-2 .custom-control-label')->pause(1000)
//      ->click('#input-group-3 .custom-control-label')->pause(2000)->type('@candidate_name', 'new name')
//      ->pause(1000)->releaseMouse()
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('在籍予測リスト');
//  }
//
//  public function test_can_print_pdf($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)
//      ->click('#input-group-3 .custom-control-label')->pause(1000)->releaseMouse()
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('在籍予測リスト')
//      ->releaseMouse()->pause(3000)
//      ->click('.btn-pdf')
//      ->pause(10000)
//      ->press('.btn-accept')
//      ->pause(3000)
//      ->releaseMouse()
//      ->pause(3000)->assertSee('PDFプレビュー')->releaseMouse()->press('.btn-close');
//  }
//
//  public function test_can_filter_result_by_user_code_role_department($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)
//      ->click('#input-group-2 .custom-control-label')->pause(1000)
//      ->type('@userCode', '1111-2222-0002')->pause(1000)
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('類似社員データ');
//  }
//
//  public function test_can_filter_list_enrollment_by_candidate_name_role_department($browser)
//  {
//    $browser->pause(1000)
//      ->releaseMouse()->pause(1000)
//      ->click('#input-group-1 .custom-control-label')->pause(2000)
//      ->click('#input-group-3 .custom-control-label')->pause(2000)->type('@candidate_name', 'new name')
//      ->pause(1000)->releaseMouse()
//      ->press('.btn-apply')
//      ->pause(5000)->assertSee('在籍予測リスト');
//  }
//  public function test_can_create_enrollment_by_role_department($browser)
//  {
//    $data = Employee::where('company_branch', 1)->limit(2)->get();
//    $browser->select('@company_branch_id', 1)->pause(1000)
//      ->type('@candidate_name', str_shuffle('new name'))->pause(1000)
//      ->type('@joining_age', 30)->pause(1000)
//      ->select('@spouse', 0)->pause(1000)
//      ->select('@dependents', 2)->pause(1000)
//      ->select('@worked_years', 3)->pause(1000)
//      ->select('@final_education', 2)->pause(1000)
//      ->type('@shortest_service', 24)->pause(1000)
//      ->releaseMouse()->pause(2000)
//      ->press('.btn-simulation')->pause(5000)->assertSee('在籍予測結果');
//    foreach ($data as $item) {
//      $browser->pause(1000)->assertSee($item->employee_name);
//    }
//  }
//}
