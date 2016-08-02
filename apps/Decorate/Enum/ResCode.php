<?php namespace Decorate\Enum;

class ResCode
{
    const SYSTEM_ERROR = 2;
    const BUCKET_NOT_EXIST = 20001;
    const UPLOAD_KEY_NOT_EXIST = 20002;
    const ADD_DIARY_FAILED = 20003;
    const ADD_FILE_FAILED = 20004;
    const FILE_BUCKET_NOT_EXIST = 20005;
    const ADD_DISCUSS_FAILED = 20006;
    const DISCUSS_NOT_EXIT = 20007;
    const COLLECTION_FAILED = 20008;
    const COLLECTION_EXIST = 20009;
    const DIARY_NOT_EXIST = 20010;
    const ADD_WORKS_FILE_FAILED = 20011;
    const SHOP_NOT_EXIST = 20012;

    public static $errorMessage = [
        self::SYSTEM_ERROR => '系统错误',
        self::BUCKET_NOT_EXIST => '存储空间不存在',
        self::UPLOAD_KEY_NOT_EXIST => '密码错误',
        self::ADD_DIARY_FAILED => '添加装修日记失败',
        self::ADD_FILE_FAILED => '添加文件失败',
        self::FILE_BUCKET_NOT_EXIST => '上传文件bucket不存在',
        self::DISCUSS_NOT_EXIT => '讨论问题不存在.',
        self::ADD_DISCUSS_FAILED => '发布问题失败',
        self::COLLECTION_FAILED => '收藏失败',
        self::COLLECTION_EXIST => '已经收藏',
        self::DIARY_NOT_EXIST => '日记不存在',
        self::ADD_WORKS_FILE_FAILED => '添加作品图片失败',
        self::SHOP_NOT_EXIST => '商铺不存在',
    ];
    
    public static function formatError($code, $msg = '')
    {
        if (!isset(static::$errorMessage[$code])) {
            $code = self::SYSTEM_ERROR;
        }
        $message = static::$errorMessage[$code];
        if ($msg) {
            $message = $msg;
        }
        return \Decorate\Utils\Help::formatResponse($code, $message);
    }
}
