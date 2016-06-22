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
}
