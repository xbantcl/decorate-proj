<?php namespace Passport\Enum;

class UserType
{
    const ORD_USER = 1;
    const SELLER = 2;
    const BOSS = 3;
    const WORKER = 4;
    const DESIGNER = 5;

    public static $avatar = [
        1 => 'http://7xp8w2.com1.z0.glb.clouddn.com/male.png',
        2 => 'http://7xp8w2.com1.z0.glb.clouddn.com/female.png'
    ];
}
