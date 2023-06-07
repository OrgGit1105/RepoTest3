<?php
/**
 * @desc off debug app:APP_DEBUG=false in file env
 */

namespace Tests\Browser;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Tests\DuskTestCase;

class EnrollmentTest extends DuskTestCase
{
  public function testGeneral()
  {
    $this->browse(function ($browser) {
      $this->login2();
      $this->test_list_enrollment_false_with_roleid_2($browser);
      $this->test_list_enrollment_success_with_roleid_2($browser);
      $this->test_create_enrollment($browser);
      $this->test_result_enrollment_with_roleid_2($browser);
      $this->test_list_enrollment_sort($browser);
      $this->test_show_enrollment_result($browser);
      $this->test_print_pdf($browser);
      $this->logout($browser);
      $this->login();
      $this->test_list_enrollment_false_with_roleid_1($browser);
      $this->test_list_enrollment_success_with_roleid_1($browser);
      $this->test_create_enrollment($browser);
      $this->test_result_enrollment_with_roleid_1($browser);
      $this->test_list_enrollment_sort($browser);
      $this->test_show_enrollment_result($browser);
      $this->test_print_pdf($browser);
    });
  }

  private function test_create_enrollment($browser)
  {
    $browser->visit('/enrollment/create')
      ->waitForText('在籍予測')
      ->releaseMouse()
      ->pause(2000)
      // all rows not null
      ->press('.btn-simulation')->pause(2000)
      // test create success
      ->select('@company_branch_id', 1)->pause(1000)
      ->press('.btn-simulation')->pause(2000)

      ->typeSlowly('@candidate_name', 'new name')->pause(1000)
      ->press('.btn-simulation')->pause(2000)

      ->typeSlowly('@joining_age', 25)->pause(1000)
      ->press('.btn-simulation')->pause(2000)

      ->select('@spouse', 1)->pause(1000)
      ->press('.btn-simulation')->pause(2000)

      ->select('@dependents', 2)->pause(1000)
      ->press('.btn-simulation')->pause(2000)

      ->select('@worked_years', 3)->pause(1000)
      ->press('.btn-simulation')->pause(2000)

      ->select('@final_education', 2)->pause(1000)
      ->press('.btn-simulation')->pause(2000)

      ->typeSlowly('@shortest_service', 1)->pause(1000)
      ->releaseMouse()->pause(1000)
      ->click('.custom-control-label')->pause(2000)
      ->press('.btn-simulation')->pause(5000)->assertSee('在籍予測結果');
  }

  private function test_result_enrollment_with_roleid_1($browser)
  {
    $id = \App\Models\Enrollment::first()->id;
    $user=User::first();
    if ($user->role_id==1){
      $browser->visit('/enrollment/result/' . $id)
        ->waitForText('在籍予測結果')
        ->releaseMouse()
        ->pause(2000)
        // test search success
        ->click('#input-group-1 .custom-control-label')->pause(1000)
        ->select('@company', 'First Department')->pause(1000)
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->typeSlowly('@userCode', '2012-1123-2131')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-3 .custom-control-label')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-1 .custom-control-label')->pause(1000)
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->typeSlowly('@userCode', '2012-1123-2131')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->click('#input-group-1 .custom-control-label')->pause(1000)
        ->select('@company', 'First Department')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-1 .custom-control-label')->pause(1000)
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->typeSlowly('@userCode', '2012-1123-2131')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('.th-header')->pause(2000)

        ->releaseMouse()
        ->click('.th-header')->pause(2000)
        ->assertSee('類似社員データ');
    }else{
      $browser->assertSee(true)->visit('/enrollment/result/' . $id)
        ->waitForText('在籍予測結果')
        ->releaseMouse()
        ->pause(2000)
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->typeSlowly('@userCode', '2012-1123-2131')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')->pause(1000)
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-3 .custom-control-label')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')->pause(1000)
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('.th-header')->pause(2000);
    }
  }

  private function test_result_enrollment_with_roleid_2($browser)
  {
    $id = \App\Models\Enrollment::first()->id;
    $user=User::first();
    if ($user->role_id==1){
      $browser->visit('/enrollment/result/' . $id)
        ->waitForText('在籍予測結果')
        ->releaseMouse()
        ->pause(2000)
        // test search success
//        ->click('#input-group-1 .custom-control-label')->pause(1000)
//        ->select('@company', 'First Department')->pause(1000)
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->typeSlowly('@userCode', '2012-1123-2131')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-3 .custom-control-label')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
//        ->click('#input-group-1 .custom-control-label')->pause(1000)
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->typeSlowly('@userCode', '2012-1123-2131')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
//        ->click('#input-group-1 .custom-control-label')->pause(1000)
//        ->select('@company', 'First Department')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
//        ->click('#input-group-1 .custom-control-label')->pause(1000)
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->typeSlowly('@userCode', '2012-1123-2131')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('.th-header')->pause(2000)

        ->releaseMouse()
        ->click('.th-header')->pause(2000)
        ->assertSee('類似社員データ');
    }else{
      $browser->assertSee(true)->visit('/enrollment/result/' . $id)
        ->waitForText('在籍予測結果')
        ->releaseMouse()
        ->pause(2000)
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->typeSlowly('@userCode', '2012-1123-2131')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')->pause(1000)
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-3 .custom-control-label')
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('#input-group-2 .custom-control-label')->pause(1000)
        ->click('#input-group-3 .custom-control-label')->pause(1000)
        ->typeSlowly('@userName', 'nguyen')->pause(1000)
        ->pause(2000)->releaseMouse()
        ->press('.btn-apply')
        ->pause(5000)

        ->releaseMouse()
        ->click('.th-header')->pause(2000);
    }
  }

  private function test_list_enrollment_false_with_roleid_1($browser){
    $browser->assertSee(true)->visit('/enrollment/index')
      ->waitForText('在籍予測リスト')
      ->releaseMouse()
      ->pause(2000)
      // test list false
      ->click('#input-group-1 .custom-control-label')
      ->pause(1000)->click('#startDate')
      ->pause(1000)->click('.rounded-circle')
      ->pause(1000)->click('#endDate')
      ->pause(1000)->click('.rounded-circle')
      ->press('.btn-apply')
      ->pause(10000)
      ->click('#input-group-2 .custom-control-label')->pause(5000)->select('.custom-select' ,2)
      ->pause(5000)
      ->press('.btn-apply')
      ->pause(5000)
      ->click('#input-group-3 .custom-control-label')->pause(5000)->typeSlowly('@candidate_name', '!^^!')
      ->pause(5000)
      ->press('.btn-apply')
      ->pause(3000)
      // No data
      ->assertSee('在籍予測リスト');
  }

  private function test_list_enrollment_success_with_roleid_1($browser){
    $browser->assertSee(true)->visit('/enrollment/index')
      ->waitForText('在籍予測リスト')
      ->releaseMouse()
      ->pause(2000)
      // test list success
      ->click('#input-group-1 .custom-control-label')
      ->pause(1000)->click('#startDate')
      ->pause(1000)->click('.rounded-circle')
      ->pause(1000)->click('#endDate')
      ->pause(1000)->click('.rounded-circle')
      ->press('.btn-apply')
      ->pause(10000)
      ->click('#input-group-2 .custom-control-label')->pause(5000)->select('.custom-select' ,2)
      ->pause(5000)
      ->press('.btn-apply')
      ->pause(5000)
      ->click('#input-group-3 .custom-control-label')->pause(5000)->typeSlowly('@candidate_name', 'test')
      ->pause(5000)
      ->press('.btn-apply')
      ->pause(3000)->assertSee('在籍予測リスト');
  }

  private function test_list_enrollment_false_with_roleid_2($browser){
    $browser->assertSee(true)->visit('/enrollment/index')
      ->waitForText('在籍予測リスト')
      ->releaseMouse()
      ->pause(2000)
      // test list false
      ->click('#input-group-1 .custom-control-label')
      ->pause(1000)->click('#startDate')
      ->pause(1000)->click('.rounded-circle')
      ->pause(1000)->click('#endDate')
      ->pause(1000)->click('.rounded-circle')
//      ->press('.btn-apply')
//      ->pause(10000)
//      ->click('#input-group-2 .custom-control-label')->pause(5000)->select('.custom-select' ,2)
      ->pause(5000)
      ->press('.btn-apply')
      ->pause(5000)
      ->click('#input-group-3 .custom-control-label')->pause(5000)->typeSlowly('@candidate_name', '!^^!')
      ->pause(5000)
      ->press('.btn-apply')
      ->pause(3000)
      // No data
      ->assertSee('在籍予測リスト');
  }

  private function test_list_enrollment_success_with_roleid_2($browser){
    $browser->assertSee(true)->visit('/enrollment/index')
      ->waitForText('在籍予測リスト')
      ->releaseMouse()
      ->pause(2000)
      // test list success
      ->click('#input-group-1 .custom-control-label')
      ->pause(1000)->click('#startDate')
      ->pause(1000)->click('.rounded-circle')
      ->pause(1000)->click('#endDate')
      ->pause(1000)->click('.rounded-circle')
//      ->press('.btn-apply')
//      ->pause(10000)
//      ->click('#input-group-2 .custom-control-label')->pause(5000)->select('.custom-select' ,2)
      ->pause(5000)
      ->press('.btn-apply')
      ->pause(5000)
      ->click('#input-group-3 .custom-control-label')->pause(5000)->typeSlowly('@candidate_name', 'test')
      ->pause(5000)
      ->press('.btn-apply')
      ->pause(3000)->assertSee('在籍予測リスト');
  }

  private function test_list_enrollment_sort($browser){
    $browser->visit('/enrollment/index')
      ->waitForText('在籍予測リスト')
      ->releaseMouse()
      ->pause(2000)
      // test list sort
      ->pause(3000)->click('.interview_date')->pause(500)->click('.interview_date')
      ->pause(500)->click('.company_branchs')->pause(500)->click('.company_branchs')
      ->pause(500)->click('.candidate_name')->pause(500)->click('.candidate_name')
      ->pause(500)->click('.joining_age')->pause(500)->click('.joining_age')
      ->pause(500)->click('.spouse')->pause(500)->click('.spouse')
      ->pause(500)->click('.dependent')->pause(500)->click('.dependent')
      ->pause(500)->click('.worked_years')->pause(500)->click('.worked_years')
      ->pause(500)->click('.final_education')->pause(500)->click('.final_education')
      ->pause(500)->click('.shortest_service')->pause(500)->click('.shortest_service')
      ->pause(500)->click('.result')->pause(500)->click('.result')
      ->pause(500)->click('.pdf')->pause(500)->click('.pdf')
      ->pause(3000)->assertSee('在籍予測リスト');
  }

  private function test_show_enrollment_result($browser){
    $browser->assertSee(true)->visit('/enrollment/index')
      ->waitForText('在籍予測リスト')
      ->releaseMouse()
      ->pause(2000)
      // test result
      ->click('.btn-result')
      ->releaseMouse()
      ->pause(3000)->assertSee('類似社員データ');
  }

  private function test_print_pdf($browser){
    $browser->assertSee(true)->visit('/enrollment/index')
      ->waitForText('在籍予測リスト')
      ->releaseMouse()
      ->pause(2000)
      // test print pdf
      ->click('.btn-pdf')
      ->pause(10000)
      ->press('.btn-accept')
      ->pause(3000)
      ->releaseMouse()
      ->pause(3000)->assertSee('PDFプレビュー');
  }

  private function logout($browser)
  {
    $browser->visit('/enrollment/index')
      ->waitForText('在籍予測リスト')
      // test logout
      ->press('.btn-logout')->pause(5000)
      ->assertSee('ログイン');
  }
}
