<?php


namespace VkAppSign;


class Checker
{
    public static function checkString(string $path, string $secret) : bool {
        $query = preg_replace('/^\?/usi', '', $path);
        $params = [];
        parse_str($query, $params);
        return self::checkParams($params, $secret);
    }

    public static function checkParams(array $params, string $secret) {
        $sign = "";

        foreach ($params as $key => $param) {
            if ($key == 'hash' || $key == 'sign' || $key == 'api_result') continue;
            if ($key == 'ad_info') {
                $param = strtr($param, [' ' => '+']);
            }
            //Костыль для случая когда к нам заходит незалогиненный пользоватлеь вк
            //это все получено в результате опыта

            if (($key === 'sid' || $key === 'secret') && $param === 'null') {
                continue;
            }

            // Иногда и такое бывает
            // access_token обычно это ключ доступа, но если это false то его не надо конкатинировать к строке
            if (($key === 'access_token') && $param === 'false') {
                continue;
            }

            if ($key === 'api_settings' && $param === 'false') {
                continue;
            }
            $sign .= (string)($param);
        }

        $sig = $secret ? hash_hmac('sha256', $sign, $secret) : 'EMPTY SECRET'.uniqid();
        $check = $params['sign'] ?? 'EMPTY SIGN'.uniqid();
        if ($sig === $check) {
            return true;
        }
        return false;
    }

    public static function checkAuthKey(string $key, int $viewerId, int $appId, string $secret) {
        return $key === md5( implode('_', [$appId, $viewerId, $secret]) );
    }

    /**
     * Рассчет подписи для данных в вызовах метода вкпей
     * $data = [
     *  'order_id' => 555,
     *  'ts' => time()
     * ]
     * @param int $merchantId
     * @param float $amount
     * @param string $description
     * @param array $data
     * @param string $vk_app_secret
     * @return array
     */
    public static function vkPayToService(
        int $merchantId,
        float $amount,
        string $description,
        array $data,
        string $vk_app_secret) {

        //Тут ключи отсортированы по алфавиту
        //это важно очень сильно
        $params = [
            "action" => "pay-to-service",
            "amount" => $amount,
            "data" => json_encode($data),
            "description" => $description,
            "merchant_id" => $merchantId,
        ];

        $sign = '';
        foreach ($params as $key => $value) {
            if ($key != 'action') {
                $sign .= ($key.'='.$value);
            }
        }
        $sign .= $vk_app_secret;
        $params['sign'] = md5($sign);
        return $params;
    }

    public static function signMerchantData(array $data, string $merchant_private_key) {
        $x = [];
        $encodedMerchantData = base64_encode(json_encode($data));
        $x['merchant_data'] = $encodedMerchantData;
        $x['merchant_sign'] = sha1($encodedMerchantData . $merchant_private_key);
        return $x;
    }
}