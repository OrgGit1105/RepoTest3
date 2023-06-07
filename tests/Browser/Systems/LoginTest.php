<?php


namespace Tests\Browser\Systems;


use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
  public function test_can_login($browser,$user)
  {
    $browser->visit('/login')
      /*
       * @desc case email or password fail
       */
      ->type('@username', 'wrong_account@gmail.com')->pause(2000)->type('@password', '123456789')->releaseMouse()
      ->press('.btn_submit')->pause(2000)->assertSee('ログイン')
      /*
       * @desc case email and password pass
       */
      ->type('@username', $user)->pause(2000)->type('@password', '12345678')->releaseMouse()->press('.btn_submit')
      ->pause(2000)
      ->assertSee('在籍予測');
  }

  public function test_can_logout($browser)
  {
    $browser->releaseMouse()->press('.btn-logout')->pause(5000)
      ->assertSee('ログイン');
  }
}
