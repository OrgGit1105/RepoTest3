<?php


namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;

class DataManagementTest extends DuskTestCase
{

  /**
   * A basic browser test example.
   *
   * @throws \Throwable
   */

  public function testGeneral()
  {
    $this->browse(function ($browser) {
      $this->login();
      $this->test_datamanagement_create($browser);
      $this->test_datamanagement_list($browser);
    });
  }

  private function test_datamanagement_create($browser)
  {
    // test create data
    $browser->visit('/csv/index')
      ->waitForText('CSVインポート')
      ->pause(3000)
      // Invalid file format
      ->attach('file_csv', __DIR__.'\FileTestData\Data_enrollment3.xls')->pause(3000)->press('.btn-import')
      ->waitUntilMissing('.alert', 5)->pause(3000)->releaseMouse()
      // Invalid column
      ->attach('file_csv', __DIR__.'\FileTestData\Data_enrollment2.csv')->pause(3000)->press('.btn-import')
      ->waitUntilMissing('.alert', 5)->pause(3000)->releaseMouse()
      // Larger than 5MB file
      ->attach('file_csv', __DIR__.'\FileTestData\Data_enrollment4.csv')->pause(3000)->press('.btn-import')
      ->waitUntilMissing('.alert', 5)->pause(3000)->releaseMouse()
      // create success
      ->attach('file_csv', __DIR__.'\FileTestData\Data_enrollment.csv')->pause(3000)->press('.btn-import')
      ->pause(3000)->assertSee('CSVインポート');
  }

  private function test_datamanagement_list($browser){
    // test list data
    $browser->visit('/datamanagement/index')
      ->waitForText('データ管理')
      ->pause(3000)->click('.employee_code')->pause(500)->click('.employee_code')
      ->pause(500)->click('.employee_name')->pause(500)->click('.employee_name')
      ->pause(500)->click('.company_branch')->pause(500)->click('.company_branch')
      ->pause(500)->click('.total_worked')->pause(500)->click('.total_worked')
      ->pause(500)->click('.date_joining_company')->pause(500)->click('.date_joining_company')
      ->pause(500)->click('.date_out_company')->pause(500)->click('.date_out_company')
      ->pause(500)->click('.joining_age_company')->pause(500)->click('.joining_age_company')
      ->pause(500)->click('.spouse')->pause(500)->click('.spouse')
      ->pause(500)->click('.dependents')->pause(500)->click('.dependents')
      ->pause(500)->click('.worked_year')->pause(500)->click('.worked_year')
      ->pause(500)->click('.final_education')->pause(500)->click('.final_education')
      ->pause(500)->click('.shortest_service')->pause(500)->click('.shortest_service')
      ->pause(3000)->assertSee('データ管理');
  }


  //  public function testGeneral()
//  {
//    $this->login();
////    $this->withExceptionHandling();
//    $this->browse(function ($browser)  {
//      $browser->visit('/#/datamanagement/index')
//        ->pause(3000)->assertSee(true)->pause(3000)->visit('/#/csv/index')
//      ->pause(3000)
//      ->attach('file_csv', __DIR__.'\FileTestData\Data_enrollment3.xls')->pause(3000)->press('.btn-import')
//      ->pause(3000)->releaseMouse()
//      ->attach('file_csv', __DIR__.'\FileTestData\Data_enrollment2.csv')->pause(3000)->press('.btn-import')
//      ->pause(3000)->releaseMouse()
//      ->attach('file_csv', __DIR__.'\FileTestData\Data_enrollment.csv')->pause(3000)->press('.btn-import')
////      ->pause(3000)->releaseMouse()
////      ->attach('file_csv', __DIR__.'\FileTestData\Data_enrollment4.csv')->pause(3000)->press('.btn-import')
//      ->pause(3000);
////      $this->test_create($browser);
////      $this->test_update($browser);
////      $this->test_del($browser);
//    });
//  }
//

//  public function test_update($browser)
//  {
//    $browser->waitFor('.btn-edit', 5)->pause(500)
//      ->press('.btn-edit')->pause(500)
//      ->waitFor(5)
//      ->typeSlowly('name', 'Auto test update ' . $this->now)->pause(500)->releaseMouse()
//      ->press(".btn-success")
//      ->waitForText($this->now, 5)
//      ->assertSee($this->now)->pause(2000);
//  }
//
//  public function test_del($browser)
//  {
//    $browser->waitFor('.btn-danger', 5)->pause(500)
//      ->press('.btn-danger')->pause(2000)
//      ->waitForText('Are you sure to delete', 5)
//      ->press("Yes")
//      ->waitForText('Success', 5)
//      ->assertSee('Success')->pause(2000);
//  }
}



