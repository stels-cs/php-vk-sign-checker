<?php


class CheckerTest extends PHPUnit_Framework_TestCase
{
    protected $appId = 6196804;
    protected $appSecret = 'UURSsxO59uTyHVvSzHgW';
    protected $request = '?api_url=https://api.vk.com/api.php&api_id=6196804&api_settings=1&viewer_id=19039187&viewer_type=0&sid=e211a8bf9bad808a2a95d75721071b874ba82d07a8b0b6aaeb98f2d220deca8fd591c89a2dca1c6165b8e&secret=9c3f105f93&access_token=064affc04d119ad5798e9e8e2b24012fcad249be99712151047532d53f2dd107f24195f6d7309bceb0274&user_id=0&is_app_user=1&auth_key=7eb1471c6341ba56ff0c0dad0f8dba6b&language=0&parent_language=0&is_secure=1&ads_app_id=6196804_e7d36e80a3155f8eb0&referrer=unknown&lc_name=abe9e425&sign=17b0427e7a43f60d081487c36170ff6d052516d06341457668391a22fd7732c1&hash=';

    public function testCheckString() {
        $ok = VkAppSign\Checker::checkString($this->request, $this->appSecret);
        if (!$ok) {
            throw new \Exception("Invalid valid request");
        }
    }

    public function testCheckEmptySecretString() {
        $ok = VkAppSign\Checker::checkString($this->request, "");
        if ($ok) {
            throw new \Exception("Valid result with invalid request");
        }
    }

    public function testCheckEmptyRequestString() {
        $ok = VkAppSign\Checker::checkString("", $this->appSecret);
        if ($ok) {
            throw new \Exception("Valid result with invalid request");
        }
    }

    public function testCheckEmptyRequestAndSecretString() {
        $ok = VkAppSign\Checker::checkString("", "");
        if ($ok) {
            throw new \Exception("Valid result with invalid request");
        }
    }

    public function testCheckParams() {
        $parmas = [
            'api_url'=>'https://api.vk.com/api.php',
            'api_id'=> '6196804',
            'api_settings'=> '1',
            'viewer_id'=> '19039187',
            'viewer_type'=> '0',
            'sid'=> 'e211a8bf9bad808a2a95d75721071b874ba82d07a8b0b6aaeb98f2d220deca8fd591c89a2dca1c6165b8e',
            'secret'=> '9c3f105f93',
            'access_token'=> '064affc04d119ad5798e9e8e2b24012fcad249be99712151047532d53f2dd107f24195f6d7309bceb0274',
            'user_id'=> '0',
            'is_app_user'=> '1',
            'auth_key'=> '7eb1471c6341ba56ff0c0dad0f8dba6b',
            'language'=> '0',
            'parent_language'=> '0',
            'is_secure'=> '1',
            'ads_app_id'=> '6196804_e7d36e80a3155f8eb0',
            'referrer'=> 'unknown',
            'lc_name'=> 'abe9e425',
            'sign'=> '17b0427e7a43f60d081487c36170ff6d052516d06341457668391a22fd7732c1',
            'hash'=> ''
        ];
        $ok = VkAppSign\Checker::checkParams($parmas, $this->appSecret);
        if (!$ok) {
            throw new \Exception("Invalid valid request");
        }
    }

    public function testCheckAuthKey() {
        $key = '7eb1471c6341ba56ff0c0dad0f8dba6b';
        $viewerId = 19039187;
        $ok = VkAppSign\Checker::checkAuthKey($key, $viewerId, $this->appId, $this->appSecret);
        if (!$ok) {
            throw new \Exception("Invalid valid request");
        }
    }

    public function testNegativeCheckString() {
        $ok = VkAppSign\Checker::checkString( strtr( $this->request, ['a'=>'b'] ), $this->appSecret);
        if ($ok) {
            throw new \Exception("Invalid request think are valid");
        }
    }

    public function testNegativeCheckParams() {
        $parmas = [
            'api_url'=>'https://api.vk.com/api.php',
            'api_id'=> '6196804',
            'api_settings'=> '1',
            'viewer_id'=> '19039187',
            'viewer_type'=> '4', // < bug here
            'sid'=> 'e211a8bf9bad808a2a95d75721071b874ba82d07a8b0b6aaeb98f2d220deca8fd591c89a2dca1c6165b8e',
            'secret'=> '9c3f105f93',
            'access_token'=> '064affc04d119ad5798e9e8e2b24012fcad249be99712151047532d53f2dd107f24195f6d7309bceb0274',
            'user_id'=> '0',
            'is_app_user'=> '1',
            'auth_key'=> '7eb1471c6341ba56ff0c0dad0f8dba6b',
            'language'=> '0',
            'parent_language'=> '0',
            'is_secure'=> '1',
            'ads_app_id'=> '6196804_e7d36e80a3155f8eb0',
            'referrer'=> 'unknown',
            'lc_name'=> 'abe9e425',
            'sign'=> '17b0427e7a43f60d081487c36170ff6d052516d06341457668391a22fd7732c1',
            'hash'=> ''
        ];
        $ok = VkAppSign\Checker::checkParams($parmas, $this->appSecret);
        if ($ok) {
            throw new \Exception("Invalid request think are valid");
        }
    }

    public function testNegativeCheckAuthKey() {
        $key = '7eb1471c6341bBUGff0c0dad0f8dba6b';
        $viewerId = 19039187;
        $ok = VkAppSign\Checker::checkAuthKey($key, $viewerId, $this->appId, $this->appSecret);
        if ($ok) {
            throw new \Exception("Invalid auth key passed");
        }
    }
}
