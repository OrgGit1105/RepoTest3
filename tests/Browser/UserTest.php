<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserTest extends DuskTestCase
{

  /**
   * A Dusk test example.
   *
   * @return void
   */
  public function testGeneral()
  {
    $this->login();
    $this->browse(function ($browser) {
      $this->test_user_list($browser);
      $this->test_user_create_false($browser);
      $this->test_create_user_success($browser);
      $this->test_update_user_false($browser);
      $this->test_update_user_success($browser);
      $this->test_user_delete($browser);
      $this->logout($browser);
      $this->login2();
      $this->test_user_list($browser);
      $this->test_user_create_role_department_false($browser);
      $this->test_create_user_role_department_success($browser);
      $this->test_update_user_role_department_false($browser);
      $this->test_update_user_role_department_success($browser);
      $this->test_user_delete($browser);
    });
  }

  private function test_user_list($browser) {
    $browser->visit('/user/index')
      ->waitForText('ユーザ管理')
      ->pause(3000)
      ->assertSee('ユーザ管理');
  }
  private function test_user_create_false($browser)
  {
    $browser
      ->waitForText('ユーザ管理')
      ->releaseMouse()
      ->pause(3000)
      // Test create user all not null
      ->press('.btn-sign')->pause(3000)->select('@department_id', '')->select('@role_id', '')->type('@username', '')->type('@email', '')->type('@password', '')->press(".btn_submit")->pause(1000)
      // Test create user Email invalidate
      ->select('@department_id', '')->select('@role_id', 1)->type('@username', 'test123')->type('@email', 'test123')->type('@password', '12345678')->press(".btn_submit")->pause(1000)
      // Test create user Password must be greater than 8 characters and and less than 16
      ->select('@department_id', '')->select('@role_id', 1)->type('@username', 'test123')->type('@email', 'test123@gmail.com')->type('@password', '123456')->press(".btn_submit")->pause(1000)
      // test create user username not null
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', '')->type('@email', 'test123@gmail.com')->type('@password', '12345678')->press(".btn_submit")->pause(1000)
      // test create user email not null
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', 'sontest123')->type('@email', '')->type('@password', '12345678')->press(".btn_submit")->pause(1000)
      // test create user password not null
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', 'sontest123')->type('@email', 'test123@gmail.com')->type('@password', '')->press(".btn_submit")->pause(1000)
      // Test create user department_id and username not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', '')->type('@email', 'test123@gmail.com')->type('@password', '12345678')->press(".btn_submit")->pause(1000)
      // Test create user username and email not null
      ->select('@department_id', '')->select('@role_id', 1)->type('@username', '')->type('@email', '')->type('@password', '12345678')->press(".btn_submit")->pause(1000)
      // Test create user email and password not null
      ->select('@department_id', '')->select('@role_id', 1)->type('@username', 'test123')->type('@email', '')->type('@password', '')->press(".btn_submit")->pause(1000)
      // Test create user department_id and username not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', '')->type('@email', 'test123@gmail.com')->type('@password', '12345678')->press(".btn_submit")->pause(1000)
      // Test create user department_id and email not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', 'test123')->type('@email', '')->type('@password', '12345678')->press(".btn_submit")->pause(1000)
      // Test create user department_id and password not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', 'test123')->type('@email', 'test123@gmail.com')->type('@password', '')->press(".btn_submit")->pause(1000)
      // Test create user username and password not null
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', '')->type('@email', 'test123@gmail.com')->type('@password', '')->press(".btn_submit")->pause(1000)
      // Test create user username , email and password not null
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', '')->type('@email', '')->type('@password', '')->press(".btn_submit")->pause(1000)
      // Test create user department_id, username, email and password not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', '')->type('@email', '')->type('@password', '')->press(".btn_submit")->pause(1000)
      // test create user Email already exists
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', 'sontest123')->type('@email', 'test@gmail.com')->type('@password', '12345678')->press(".btn_submit")
      ->waitUntilMissing('.alert', 5)
      ->pause(2000)
      ->assertSee('ユーザ追加');
  }

  private function test_create_user_success($browser) {
    $browser
      ->waitForText('ユーザ追加')
      ->releaseMouse()
      ->pause(3000)
      // test create user success
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', 'test123456')->type('@email', str_shuffle('mailpasstest') . '@gmail.com')->type('@password', '12345678')->press(".btn_submit")
      ->pause(2000)
      ->assertSee('ユーザ管理');
  }
  private function test_update_user_false($browser) {
    $user = User::orderBy('id', 'desc')->first();
    $browser
      ->waitForText('ユーザ管理')
      ->releaseMouse()->pause(3000)
      ->click('#btn-edit-' . $user->id)->pause(3500)
      ->releaseMouse()->pause(3000)
      // Test Update all not null
      ->select('@department_id', '')->select('@role_id', '')->type('@username', '')->type('@email', '')->type('@password', '')->press("@submit")->pause(1000)
      // Test create user Email invalidate
      ->select('@department_id', '')->select('@role_id', 1)->type('@username', 'test123')->type('@email', 'test123')->type('@password', '12345678')->press("@submit")->pause(1000)
      // Test create user Password must be greater than 8 characters and and less than 16
      ->select('@department_id', '')->select('@role_id', 1)->type('@username', 'test123')->type('@email', 'test123@gmail.com')->type('@password', '123456')->press("@submit")->pause(1000)
      // test create user username not null
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', '')->type('@email', 'test123@gmail.com')->type('@password', '12345678')->press("@submit")->pause(1000)
      // test create user email not null
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', 'sontest123')->type('@email', '')->type('@password', '12345678')->press("@submit")->pause(1000)
      // Test create user department_id and username not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', '')->type('@email', 'test123@gmail.com')->type('@password', '12345678')->press("@submit")->pause(1000)
      // Test create user username and email not null
      ->select('@department_id', '')->select('@role_id', 1)->type('@username', '')->type('@email', '')->type('@password', '12345678')->press("@submit")->pause(1000)
      // Test create user email and password not null
      ->select('@department_id', '')->select('@role_id', 1)->type('@username', 'test123')->type('@email', '')->type('@password', '')->press("@submit")->pause(1000)
      // Test create user department_id and username not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', '')->type('@email', 'test123@gmail.com')->type('@password', '12345678')->press("@submit")->pause(1000)
      // Test create user department_id and email not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', 'test123')->type('@email', '')->type('@password', '12345678')->press("@submit")->pause(1000)
      // Test create user username , email and password not null
      ->select('@department_id', 2)->select('@role_id', 2)->type('@username', '')->type('@email', '')->type('@password', '')->press("@submit")->pause(1000)
      // Test create user department_id, username, email and password not null
      ->select('@department_id', '')->select('@role_id', 2)->type('@username', '')->type('@email', '')->type('@password', '')->press("@submit")->pause(1000)
      ->pause(3000)
      ->assertSee('ユーザ編集');
  }

  private function test_update_user_success($browser) {
    $browser
      ->waitForText('ユーザ編集')
      ->releaseMouse()->pause(3000)
      // test update user Headquater success
      ->select('@role_id', 2)->select('@department_id', 2)->type('@username', 'test3')->type('@email', str_shuffle('mailpasstest') . '@gmail.com')->type('@password', '12345678')->click('@submit')->pause(2000)
      ->assertSee('ユーザ管理');
  }

  private function test_user_delete($browser)
  {
    $browser
      ->waitForText('ユーザ管理')
      ->releaseMouse()->pause(3000)
      // Test delete user Email
      ->press('.btn-delete')
      ->pause(2000)
      ->press('.btn-accept')
      ->pause(2000)
      ->assertSee('ユーザ管理');
  }

  private function logout($browser)
  {
    $browser->visit('/enrollment/index')
      ->waitForText('在籍予測')
      // test logout
      ->press('.btn-logout')->pause(5000)
      ->assertSee('ログイン');
  }

  private function test_user_create_role_department_false($browser)
  {
    $browser->visit('/user/index')
      ->waitForText('ユーザ管理')
      ->releaseMouse()
      ->pause(3000)
      // Test create user Email invalidate
      ->press('.btn-sign')->pause(3000)->type('@username', 'test123')->type('@email', 'test123')->type('@password', '12345678')->press(".btn_submit")->pause(3000)
      // Test create user all not null
      ->type('@username', '')->type('@email', '')->type('@password', '')->press(".btn_submit")->pause(3000)
      // Test create user Password must be greater than 8 characters and and less than 16
      ->type('@username', 'test123')->type('@email', 'test123@gmail.com')->type('@password', '123456')->press(".btn_submit")->pause(3000)
      // test create user username not null
      ->type('@username', '')->type('@email', 'test123@gmail.com')->type('@password', '12345678')->press(".btn_submit")->pause(3000)
      // test create user email not null
      ->type('@username', 'sontest123')->type('@email', '')->type('@password', '12345678')->press(".btn_submit")->pause(3000)
      // test create user password not null
      ->type('@username', 'sontest123')->type('@email', 'test123@gmail.com')->type('@password', '')->press(".btn_submit")->pause(3000)
      // test create user password and email not null
      ->type('@username', 'sontest123')->type('@email', '')->type('@password', '')->press(".btn_submit")->pause(3000)
      // test create user username and email not null
      ->type('@username', '')->type('@email', '')->type('@password', '12345678')->press(".btn_submit")->pause(3000)
      // test create user username and password not null
      ->type('@username', '')->type('@email', 'test3@gmail.com')->type('@password', '')->press(".btn_submit")->pause(3000)
      // test create user username, password and email not null
      ->type('@username', '')->type('@email', '')->type('@password', '')->press(".btn_submit")->pause(3000)
      // test create user Email already exists
      ->type('@username', 'sontest123')->type('@email', 'test@gmail.com')->type('@password', '12345678')->press(".btn_submit")
      ->waitUntilMissing('.alert', 5)
      ->pause(2000)
      ->assertSee('ユーザ追加');
  }

  private function test_create_user_role_department_success($browser) {
    $browser
      ->waitForText('ユーザ追加')
      ->releaseMouse()
      ->pause(3000)
      // test create user success
      ->type('@username', 'test1234567')->type('@email', 'test1234567@gmail.com')->type('@password', '12345678')->press(".btn_submit")
      ->pause(2000)
      ->assertSee('ユーザ管理');
  }

  private function test_update_user_role_department_false($browser)
  {
    $browser->visit('/user/index')
      ->waitForText('ユーザ管理')
      ->releaseMouse()->pause(3000)
      ->click('@btn-edit')->pause(3500)
      // test update user role department username and email not null
      ->type('@username', '')->pause(3000)->type('@email', '')->pause(3000)->type('@password', '12345678')->pause(1000)->click("@submit")->releaseMouse()->pause(3000)
      // test update user role department username not null
      ->type('@username', '')->pause(1000)->type('@email', 'test3@gmail.com')->pause(1000)->type('@password', '12345678')->pause(1000)->click("@submit")->releaseMouse()->pause(3000)
      // test update user role department email not null
      ->type('@username', 'test')->pause(1000)->type('@email', '')->pause(1000)->type('@password', '12345678')->pause(1000)->click("@submit")->pause(2000)
//      // test update user role department email not null
      ->type('@username', 'test2')->pause(1000)->type('@email', '')->pause(1000)->type('@password', '12345678')->pause(1000)->click("@submit")->releaseMouse()->pause(3000)
//      // test update user role department username not null
      ->type('@username', '')->pause(1000)->type('@email', 'test@gmail.com')->pause(1000)->type('@password', '12345678')->pause(1000)->click("@submit")->releaseMouse()->pause(3000)
//      // test update user role department username and email not null
      ->type('@username', '')->pause(1000)->type('@email', '')->pause(1000)->type('@password', '123456789')->pause(1000)->click("@submit")->releaseMouse()->pause(3000)
      ->assertSee('ユーザ編集');
  }

  private function test_update_user_role_department_success($browser) {
    $browser
      ->waitForText('ユーザ編集')
      ->releaseMouse()->pause(3000)
      // test test update user role department success
      ->type('@username', 'test2')->type('@email', 'test2@gmail.com')->type('@password', '12345678')->click('@submit')->pause(2000)
      ->assertSee('ユーザ管理');
  }

}
