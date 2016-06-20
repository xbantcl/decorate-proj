<?php namespace Passport\Modules;
/**
 * BaseModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

abstract class BaseModule
{
    public static $instance = [];

    /**
     * 获取modules实例.
     * 
     * @return BaseModule
     */
    public static function getInstance()
    {
        $className = get_called_class();
        if (!isset(static::$instance[$className])) {
            static::$instance[$className] = new $className;
        }
        return static::$instance[$className];
    }
}