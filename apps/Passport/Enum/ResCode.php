<?php namespace Passport\Enum;

class ResCode
{
    const SYSTEM_ERROR = 2;
    const ACCOUNT_NOT_EXIST = 10001;
    const PASSWORD_ERROR = 10002;
    const ACCOUNT_EXIST = 10003;
    const INVALID_SESSION = 10004;
    const INVALID_PLATFORM = 10005;
    const INVALID_USER_TYPE = 10006;
    const UPDATE_USER_INFO_FAILED = 10007;

    public static $errorMessage = [
            self::SYSTEM_ERROR => '系统错误',
            self::ACCOUNT_NOT_EXIST => '帐号不存在',
            self::PASSWORD_ERROR => '密码错误',
            self::ACCOUNT_EXIST => '帐号存在',
            self::INVALID_SESSION => '无效的sesion,请重新登录',
            self::INVALID_PLATFORM => '无效的平台',
            self::INVALID_USER_TYPE => '无效的用户类型',
            self::UPDATE_USER_INFO_FAILED => '更新用户失败',
    ];

    public static function formatError($code)
    {
        if (!isset(static::$errorMessage[$code])) {
            $code = self::SYSTEM_ERROR;
        }
        $message = static::$errorMessage[$code];
        return \Passport\Utils\Help::formatResponse($code, $message);
    }
}
