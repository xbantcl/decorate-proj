<?php namespace Decorate\Utils;

class Help
{
    /**
     * 获取密码盐值.
     * 
     * @return string
     */
    public static function getSalt()
    {
        return substr(md5(\uniqid(mt_rand(1, 10000000))), 0, 11);
    }

    /**
     * 加密密码.
     * 
     * @param string $password
     * @param string $salt
     * 
     * @return string
     */
    public static function encryptPassword($password, $salt)
    {
        return md5(md5($password) . $salt);
    }

    public static function isPhone($account)
    {
        return preg_match('/^1[3|5|7|8|][0-9]{9}/', $account);
    }

    public static function isEmail($account)
    {
        return preg_match('#[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}#', $account);
    }

    public static function getRandomStr($length = 32)
    {
        $charset = 'abcdefghijklmnopkrstuvwhyzABCDEFGHIJKLMNOPKRSTUVWHYZ0123456789~!@#$%^&*';
        $charset = str_shuffle($charset);
        return substr($charset, 0, $length);
    }

    public static function response($response, $data = null, $code = 0, $message = 'success') {
        return $response->write(json_encode(
            [
                'error_code' => $code,
                'message' => $message,
                'data' => $data
            ]
        ));
    }

    public static function formatResponse($code, $message)
    {
        return ['code' => $code, 'message' => $message];
    }

    public static function getParams($request, $uid) {
        return array_merge($request->getParams(), ['uid' => $uid]);
    }

    public static function config($key)
    {
        $configName = __DIR__ . '/../config.php';
        if (!file_exists($configName)) {
            return false;
        }
        $configs = require $configName;
        if (!isset($configs[$key])) {
            return false;
        }
        return $configs[$key];
    }
}