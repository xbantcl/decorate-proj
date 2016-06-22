<?php namespace Passport\Enum;

class HouseType
{
    const ONE_ROOM = 1;
    const TWO_ROOM = 2;
    const THREE_ROOM = 3;
    const FOUR_ROOM = 4;
    const FIVE_ROOM = 5;

    public static $houseStyle = [
        self::ONE_ROOM => '一居室',
        self::TWO_ROOM => '二居室',
        self::THREE_ROOM => '三居室',
        self::FOUR_ROOM => '四居室',
        self::FIVE_ROOM => '五居室'
    ];

    public static function getHouseStyleName($houseType)
    {
        if (!isset(static::$houseStyle[$houseType])) {
            return '未填写';
        }
        return static::$houseStyle[$houseType];
    }
}
