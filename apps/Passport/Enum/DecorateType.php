<?php namespace Passport\Enum;

class DecorateType
{
    const JES = 1;

    public static $decorateStyle = [
        self::JES => '简欧',
    ];

    public static function getDecorateStyleName($decorateType)
    {
        if (!isset(static::$decorateStyle[$decorateType])) {
            return '未填写';
        }
        return static::$decorateStyle[$decorateType];
    }

    /*********** 装修状态 **********/
    const DECORATE_READY = 1; //装修准备中
    const DECORATING = 2; // 装修进场
    const DECORATE_FINISH = 3; // 装修完成

    public static $decorateStatus = [
        self::DECORATE_READY => '装修准备中',
        self::DECORATING => '装修进场',
        self::DECORATE_FINISH => '装修完成'
    ];

    public static function getDecorateStatus($status) {
        if (!isset(static::$decorateStatus[$status])) {
            return '未知状态';
        }
        return static::$decorateStatus[$status];
    }
}
