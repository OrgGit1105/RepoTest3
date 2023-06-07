<?php


namespace Tests\Browser\Systems;


use App\Models\User;
use Tests\DuskTestCase;

class UserTest extends DuskTestCase
{
  public function test_link_to_page_create_user($browser)
  {
    $browser->releaseMouse()
      ->pause(1000)
      ->press('.btn-sign')
      ->pause(5000)->assertSee('ユーザ追加');
  }

  public function test_can_create_user($browser)
  {
    /*
    * @desc case email password fail format
    */
    $browser->select('@role_id', 2)
      ->select('@department_id', 10)
      ->type('@username', str_shuffle('test username'))
      ->type('@email', 'mail fail')->type('@password', '123456')->press(".btn_submit")->pause(2000)->assertSee('ユーザ追加')
      /*
      * @desc case pass
      */
      ->type('@email', str_shuffle('mailpasstest') . '@gmail.com')
      ->type('@password', '12345678')->press(".btn_submit")->pause(4000)
      ->assertSee('ユーザ管理');
  }

  public function test_link_to_edit_user($browser)
  {
    $user = User::orderBy('id', 'desc')->first();
    $browser->releaseMouse()
      ->pause(1000)
      ->press('#btn-edit-' . $user->id)
      ->pause(5000)->assertSee('ユーザ編集');
  }

  public function test_can_edit_user($browser)
  {
    $browser->type('@email', str_shuffle('mailpassedit') . '@gmail.com')
      ->press("@submit")->pause(4000)->assertSee('ユーザ管理');;
  }

  public function test_can_remove_user($browser)
  {
    $user = User::orderBy('id', 'desc')->first();
    $browser->releaseMouse()
      ->pause(1000)
      ->press('#btn-remove-' . $user->id)
      ->pause(5000)->releaseMouse()->press('.btn-accept')->pause(2000)->assertSee(true);
  }

  public function test_can_create_user_department($browser)
  {
    /*
  * @desc case email password fail format
  */
    $browser->type('@username', str_shuffle('test username'))
      ->type('@email', 'mail fail')->type('@password', '123456')->press(".btn_submit")->pause(2000)->assertSee('ユーザ追加')
      /*
      * @desc case pass
      */
      ->type('@email', str_shuffle('mailpasstest') . '@gmail.com')
      ->type('@password', '12345678')->press(".btn_submit")->pause(4000)
      ->assertSee('ユーザ管理');
  }

  public function test_can_link_user_edit_role_department($browser)
  {
    $user = User::where('role_id', 2)->orderBy('id', 'desc')->first();
    $browser->releaseMouse()
      ->pause(1000)
      ->press('#btn-edit-' . $user->id)
      ->pause(5000)->assertSee('ユーザ編集');
  }

  public function test_can_remove_user_role_department($browser)
  {
    $user = User::where('role_id', 2)->orderBy('id', 'desc')->first();
    $browser->releaseMouse()
      ->pause(1000)
      ->press('#btn-remove-' . $user->id)
      ->pause(5000)->releaseMouse()->press('.btn-accept')->pause(2000)->assertSee(true);
  }
}
