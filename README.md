# Проверка подписи запроса для приложений в вк

[vk.com/dev/community_apps_docs](https://vk.com/dev/community_apps_docs)


```php
composer require stels-cs/php-vk-sign-checker
```

```php
$request = "?api_url=https://api.vk.com/api.php&api_id=6196804&api_settings=1&viewer_id=19039187&viewer_type=0&sid=e211a8bf9bad808a2a95d75721071b874ba82d07a8b0b6aaeb98f2d220deca8fd591c89a2dca1c6165b8e&secret=9c3f105f93&access_token=064affc04d119ad5798e9e8e2b24012fcad249be99712151047532d53f2dd107f24195f6d7309bceb0274&user_id=0&is_app_user=1&auth_key=7eb1471c6341ba56ff0c0dad0f8dba6b&language=0&parent_language=0&is_secure=1&ads_app_id=6196804_e7d36e80a3155f8eb0&referrer=unknown&lc_name=abe9e425&sign=17b0427e7a43f60d081487c36170ff6d052516d06341457668391a22fd7732c1&hash=";
$appSecret = 'UURSsxO59uTyHVvSzHgW';
$ok = VkAppSign\Checker::checkString($request, $appSecret);
if ($ok) {
//подпись валидна запрос не изменен
} else {
//ОИШИБКА, запрос был изменен или неверный $appSecret
}
```


Рассчет подписи для платежей VkPay

```php

$data = [
    'order_id' => 555,
    'ts' => time(),
];

$amount = 100;

$merchantId = 5556677; //Fake
$secret = 'DervCzxvwetgtvDFSGesrtbsrtbsvesr'; //Fake
$description = "TestPay";
$params = \VkAppSign\Checker::vkPayToService($merchantId, $amount, $description, $data, $secret);

$params //надо передать в openExternalApp на стороне клиента

//Дебаг
$ss = json_encode($params, JSON_UNESCAPED_UNICODE);
$tmp = "VK.callMethod(\"openExternalApp\", \"vkpay\", {$ss})";
echo $tmp; //
```

Пример Request для Laravel

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use VkAppSign\Checker;

class SignRequest extends FormRequest
{
    public $groupId;
    public $viewerType;
    public $userId;
    public $hash;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $url = $this->header('X-vk-sign', $this->header('x-vk-sign', ''));
        $launchParameters = $this->parseLaunchParametersUrl($url);
        $areParametersValid = Checker::checkParams($launchParameters, config('app.vk_app_secret'));
        if (!$areParametersValid) {
            return false;
        }
        $this->groupId = !empty($launchParameters['group_id']) ? (int)$launchParameters['group_id'] : null;
        $this->viewerType = (int)$launchParameters['viewer_type'];
        $this->userId = (int)$launchParameters['viewer_id'];
        $this->hash = (string)$launchParameters['hash'];
        return true;
    }

    public function parseLaunchParametersUrl($url)
    {
        $query = preg_replace('/^\?/usi', '', $url);
        $params = [];
        parse_str($query, $params);
        return $params;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    public function int($key, $def = 0): int
    {
        return (int)$this->get($key, $def);
    }

    public function string($key, $def = '')
    {
        return trim((string)$this->get($key, $def));
    }

    public function getArray($key, $def = [])
    {
        return (array)$this->get($key, $def);
    }
}
```
