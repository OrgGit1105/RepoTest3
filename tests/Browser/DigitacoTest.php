<?php


namespace Tests\Browser;


use App\Models\DigitacoFile;
use Carbon\Carbon;
use Tests\DuskTestCase;

class DigitacoTest extends DuskTestCase
{
  public function testGeneral()
  {
    $this->browse(function ($browser) {
      $this->login();
      $this->test_can_view_digitaco_datamanagement_detail_point($browser);
      $this->test_can_view_digitaco_datamanagement_detail_driving($browser);
    });
  }

  private function test_can_view_digitaco_datamanagement_detail_point($browser)
  {
    $browser->visit('/datadigitaco/index')
      ->waitForText('データ管理')
      ->pause(3000)->releaseMouse()
      ->click('#file_name_data_point')
      ->pause(3000)
      ->assertSee('社員名');
  }
  private function test_can_view_digitaco_datamanagement_detail_driving($browser)
  {
    $browser
      ->visit('/datadigitaco/index')
      ->waitForText('データ管理')
      ->pause(3000)->releaseMouse()
      ->click('#file_name_data_driving')
      ->pause(3000)
      ->assertSee('順位');
  }
}
