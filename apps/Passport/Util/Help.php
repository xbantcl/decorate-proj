<?php namespace Passport\Util;

class Help
{
    /**
     * 获取密码盐值.
     * 
     * @return string
     */
    public static function getSalt()
    {
        return md5(\uniqid(mt_rand(1, 10000000)));
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
}