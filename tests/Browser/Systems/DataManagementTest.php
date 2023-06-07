<?php


namespace Tests\Browser\Systems;


use Tests\DuskTestCase;

class DataManagementTest extends DuskTestCase
{
  public function test_can_import_csv($browser)
  {
    // Invalid file format
    $browser->attach('file_csv', __DIR__ . '\FileTestData\Data_enrollment3.xls')->pause(3000)->press('.btn-import')
      ->pause(3000)->releaseMouse()
      // Invalid column
      ->attach('file_csv', __DIR__ . '\FileTestData\Data_enrollment2.csv')->pause(3000)->press('.btn-import')
      ->pause(3000)->releaseMouse()
      ->attach('file_csv', __DIR__ . '\FileTestData\Data_enrollment.csv')->pause(3000)->press('.btn-import')
      // create success
      ->pause(3000)->assertSee('CSVインポート');
  }

  public function test_can_link_to_digitaco_point($browser)
  {
    $browser->pause(2000)->releaseMouse()
      ->click('#my-table .digitaco-point')
      ->pause(5000)->assertSee('データ管理');
  }
  public function test_can_link_to_digitaco_driving($browser)
  {
    $browser->pause(2000)->releaseMouse()
      ->click('#my-table .digitaco-driving')
      ->pause(5000)->assertSee('データ管理');
  }
}
