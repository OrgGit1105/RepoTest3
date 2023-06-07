<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
  /**
   * A Dusk test example.
   *
   * @return void
   */
  public function testGeneral()
  {
    $this->browse(function ($browser) {
      $this->test_login_false($browser);
      $this->test_login_success($browser);
      $this->test_logout($browser);
    });
  }
  // test_login_false ()
  private function test_login_false($browser)
  {
    $browser->visit('/login')
      ->waitForText('ログイン')
      ->releaseMouse()
      ->pause(3000)
      // test Password must be greater than 8 characters and and less than 16
      ->typeSlowly('@username', 'Goodmoning@gmail.com')->typeSlowly('@password', '123456')->releaseMouse()->press('.btn_submit')->pause(2000)
      // Email invalidate
      ->typeSlowly('@username', 'test_email')->releaseMouse()->typeSlowly('@password', '12345678')->press('.btn_submit')->pause(2000)
      //test Password must be greater than 8 characters and and less than 16  Email invalidate
      ->typeSlowly('@username', 'test_email')->releaseMouse()->typeSlowly('@password', '123456')->press('.btn_submit')->pause(2000)
      // test email not null
      ->typeSlowly('@username', '')->releaseMouse()->typeSlowly('@password', '123456')->press('.btn_submit')->pause(2000)
      // test password not null
      ->typeSlowly('@username', 'test_email@gmail.com')->releaseMouse()->typeSlowly('@password', '')->press('.btn_submit')->pause(2000)
      // test email and password not null
      ->typeSlowly('@username', '')->releaseMouse()->typeSlowly('@password', '')->press('.btn_submit')->pause(2000)
      // Wrong account or password
      ->typeSlowly('@username', 'wrong_account@gmail.com')->typeSlowly('@password', '12345678')->pause(1000)
      ->press('.btn_submit')
      ->waitUntilMissing('.alert', 5)
      ->pause(2000)
      ->assertSee('ログイン');

  }

  private function test_login_success($browser)
  {
    $browser
      ->waitForText('ログイン')
      ->releaseMouse()
      ->pause(3000)
      //test login success
      ->typeSlowly('@username', 'test@gmail.com')->typeSlowly('@password', '12345678')->press('.btn_submit')
      ->pause(5000)
      ->assertSee('在籍予測');
  }

  private function test_logout($browser)
  {
      $browser
        ->waitForText('在籍予測')
        ->releaseMouse()
        ->pause(3000)
        // test logout
        ->press('@btn_logout')->pause(5000)
        ->assertSee('ログイン');
  }
}
