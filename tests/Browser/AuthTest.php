<?php
//
//namespace Tests\Browser;
//
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Laravel\Dusk\Browser;
//use Tests\DuskTestCase;
//
//class AuthTest extends DuskTestCase
//{
//  /**
//   * A Dusk test example.
//   *
//   * @return void
//   */
//  public function testGeneral()
//  {
//    $this->browse(function ($browser) {
//      $this->test_login_false($browser);
//      $this->test_login_success($browser);
//    });
//  }
//
//  private function test_login_false($browser)
//  {
//    $browser->visit('/login')
//      ->releaseMouse()
//      ->pause(3000)
//      ->typeSlowly('@username', 'Good morning!')
//      ->typeSlowly('@password', '123456')
//      ->releaseMouse()
//      ->press('.btn_submit')
//      ->pause(2000)
//      ->waitUntilMissing('.alert', 5)
//      ->typeSlowly('@username', '<script>alert("this is bad script")</script>')
//      ->releaseMouse()
//      ->typeSlowly('@password', '123456')
//      ->press('.btn_submit')
//      ->pause(2000)
//      ->waitUntilMissing('.alert', 5)
//      ->typeSlowly('@username', 'wrong_account@gmail.com')->typeSlowly('@password', '12345678')->releaseMouse()
//      ->pause(1000)
//      ->press('.btn_submit')
//      ->pause(2000);
//  }
//
//  private function test_login_success($browser)
//  {
//    $browser
//      ->releaseMouse()
//      ->pause(3000)
//      ->typeSlowly('@username', 'test@gmail.com')
//      ->typeSlowly('@password', '12345678')
//      ->press('.btn_submit')
//      ->pause(2000)
//      ->assertSee(true);
//  }
//
//
//}
