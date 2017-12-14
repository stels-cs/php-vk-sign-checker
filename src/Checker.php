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
            $sign .= ($param);
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
}