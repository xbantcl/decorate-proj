<?php namespace Decorate\Enum;

class ResCode
{
    const SYSTEM_ERROR = 2;
    const BUCKET_NOT_EXIST = 20001;
    const UPLOAD_KEY_NOT_EXIST = 20002;
    const ADD_DIARY_FAILED = 20003;
    const ADD_FILE_FAILED = 20004;
    const FILE_BUCKET_NOT_EXIST = 20005;

    public static $errorMessage = [
        self::SYSTEM_ERROR => '系统错误',
        self::BUCKET_NOT_EXIST => '存储空间不存在',
        self::UPLOAD_KEY_NOT_EXIST => '密码错误',
        self::ADD_DIARY_FAILED => '添加装修日记失败',
        self::ADD_FILE_FAILED => '添加文件失败',
        self::FILE_BUCKET_NOT_EXIST => '上传文件bucket不存在',
    ];
    
    public static function formatError($code)
    {
        if (!isset(static::$errorMessage[$code])) {
            $code = self::SYSTEM_ERROR;
        }
        $message = static::$errorMessage[$code];
        return \Decorate\Utils\Help::formatResponse($code, $message);
    }
}
