<?php

use PHPUnit\Framework\TestCase;
use VkAppSign\FakeUser;

class CheckerTest extends TestCase {
  protected $appId = 6196804;
  protected $appSecret = 'UURSsxO59uTyHVvSzHgW';
  protected $request = '?api_url=https://api.vk.com/api.php&api_id=6196804&api_settings=1&viewer_id=19039187&viewer_type=0&sid=e211a8bf9bad808a2a95d75721071b874ba82d07a8b0b6aaeb98f2d220deca8fd591c89a2dca1c6165b8e&secret=9c3f105f93&access_token=064affc04d119ad5798e9e8e2b24012fcad249be99712151047532d53f2dd107f24195f6d7309bceb0274&user_id=0&is_app_user=1&auth_key=7eb1471c6341ba56ff0c0dad0f8dba6b&language=0&parent_language=0&is_secure=1&ads_app_id=6196804_e7d36e80a3155f8eb0&referrer=unknown&lc_name=abe9e425&sign=17b0427e7a43f60d081487c36170ff6d052516d06341457668391a22fd7732c1&hash=';

  public function testCheckString() {
    $ok = VkAppSign\Checker::checkString($this->request, $this->appSecret);
    $this->assertTrue($ok);
  }

  public function testCheckEmptySecretString() {
    $ok = VkAppSign\Checker::checkString($this->request, "");
    $this->assertFalse($ok);
  }

  public function testCheckEmptyRequestString() {
    $ok = VkAppSign\Checker::checkString("", $this->appSecret);
    $this->assertFalse($ok);
  }

  public function testCheckEmptyRequestAndSecretString() {
    $ok = VkAppSign\Checker::checkString("", "");
    $this->assertFalse($ok);
  }

  public function testCheckParams() {
    $parmas = [
      'api_url'         => 'https://api.vk.com/api.php',
      'api_id'          => '6196804',
      'api_settings'    => '1',
      'viewer_id'       => '19039187',
      'viewer_type'     => '0',
      'sid'             => 'e211a8bf9bad808a2a95d75721071b874ba82d07a8b0b6aaeb98f2d220deca8fd591c89a2dca1c6165b8e',
      'secret'          => '9c3f105f93',
      'access_token'    => '064affc04d119ad5798e9e8e2b24012fcad249be99712151047532d53f2dd107f24195f6d7309bceb0274',
      'user_id'         => '0',
      'is_app_user'     => '1',
      'auth_key'        => '7eb1471c6341ba56ff0c0dad0f8dba6b',
      'language'        => '0',
      'parent_language' => '0',
      'is_secure'       => '1',
      'ads_app_id'      => '6196804_e7d36e80a3155f8eb0',
      'referrer'        => 'unknown',
      'lc_name'         => 'abe9e425',
      'sign'            => '17b0427e7a43f60d081487c36170ff6d052516d06341457668391a22fd7732c1',
      'hash'            => '',
    ];
    $ok     = VkAppSign\Checker::checkParams($parmas, $this->appSecret);
    $this->assertTrue($ok);
  }

  public function testCheckAuthKey() {
    $key      = '7eb1471c6341ba56ff0c0dad0f8dba6b';
    $viewerId = 19039187;
    $ok       = VkAppSign\Checker::checkAuthKey($key, $viewerId, $this->appId, $this->appSecret);
    $this->assertTrue($ok);
  }

  public function testNegativeCheckString() {
    $ok = VkAppSign\Checker::checkString(strtr($this->request, ['a' => 'b']), $this->appSecret);
    $this->assertFalse($ok);
  }

  public function testNegativeCheckParams() {
    $parmas = [
      'api_url'         => 'https://api.vk.com/api.php',
      'api_id'          => '6196804',
      'api_settings'    => '1',
      'viewer_id'       => '19039187',
      'viewer_type'     => '4', // < bug here
      'sid'             => 'e211a8bf9bad808a2a95d75721071b874ba82d07a8b0b6aaeb98f2d220deca8fd591c89a2dca1c6165b8e',
      'secret'          => '9c3f105f93',
      'access_token'    => '064affc04d119ad5798e9e8e2b24012fcad249be99712151047532d53f2dd107f24195f6d7309bceb0274',
      'user_id'         => '0',
      'is_app_user'     => '1',
      'auth_key'        => '7eb1471c6341ba56ff0c0dad0f8dba6b',
      'language'        => '0',
      'parent_language' => '0',
      'is_secure'       => '1',
      'ads_app_id'      => '6196804_e7d36e80a3155f8eb0',
      'referrer'        => 'unknown',
      'lc_name'         => 'abe9e425',
      'sign'            => '17b0427e7a43f60d081487c36170ff6d052516d06341457668391a22fd7732c1',
      'hash'            => '',
    ];
    $ok     = VkAppSign\Checker::checkParams($parmas, $this->appSecret);
    $this->assertFalse($ok);
  }

  public function testNegativeCheckAuthKey() {
    $key      = '7eb1471c6341bBUGff0c0dad0f8dba6b';
    $viewerId = 19039187;
    $ok       = VkAppSign\Checker::checkAuthKey($key, $viewerId, $this->appId, $this->appSecret);
    $this->assertFalse($ok);
  }

  public function testUnauthorizedUser() {
    //Неавторизованные в вк пользователи
    //Это особенная категория у них параметры
    //sid, secret и api_settings равны false или null
    //их не надо учитывать в подписи запроса
    $r  = '?user_id=0&api_url=https://api.vk.com/api.php&api_id=5990572&api_settings=false&viewer_id=0&viewer_type=0&access_token=6a31f467312d91e96a55b4a1336a6a9ccb66a316a31f467312ae06907293238948266bb&is_app_user=0&auth_key=c3dc47ba866e9ce0dd51ce97dbfd01bb&language=0&parent_language=0&is_secure=1&sid=null&secret=null&stats_hash=57ea812c7023558a6b&group_id=167021499&source=layer&lc_name=e48f5881&ads_app_id=5990572_f1da7822b63d66b589&sign=5426a723893c5507b62dec3a893979512249b6d078b055af04bdac921a2a93c2&hash=';
    $ok = VkAppSign\Checker::checkString($r, 'y0xvZ38sBIrYIPWlup64');
    $this->assertTrue($ok);
  }

  public function testVkAppsSign() {
    $secret  = "rkwdOT04kUh28RDEC9zr";
    $request = "?vk_access_token_settings=friends%2Cgroups&vk_app_id=6825462&vk_are_notifications_enabled=0&vk_is_app_user=1&vk_language=ru&vk_platform=desktop_web&vk_user_id=19039187&sign=vBBPIysvzccFUn_e55JCGxZBnmxpXeh92XpiAY9gcv8";

    $this->assertTrue(VkAppSign\Checker::checkVkAppsSign($request, $secret));
  }

  public function testFakeUser1() {
    $user = new FakeUser();
    $user->setAppId(555)->setUserId(555);
    $secret = "s45g";
    $signedArgs = $user->sign($secret);
    $isValid = VkAppSign\Checker::checkVkAppsParams($signedArgs, $secret);
    $this->assertTrue( $isValid );
  }

  public function testFakeUser2() {
    $user = new FakeUser();
    $user->setAppId(555)->setUserId(555)->setGroupId(12, "admin");
    $secret = "s45g";
    $signedArgs = $user->signToUrl($secret);
    $isValid = VkAppSign\Checker::checkVkAppsSign($signedArgs, $secret);
    $this->assertTrue( $isValid );
  }
}
