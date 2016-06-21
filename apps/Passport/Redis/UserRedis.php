<?php namespace Passport\Redis;

use Passport\Utils\Help;
use Respect\Validation\Rules\NullType;
/**
 * User Redis
 */

class UserRedis extends BaseRedis
{
    const WEB_SESSION_PREFIX = 'web_session#';
    const APP_SESSION_PREFIX = 'app_session#';
    const ONLINE_COUNT = 3;
    const SESSION_EXPIRE_TIME = 2592000;

    private static $userInstance = NULL;

    public static function getInstance($config = 'default')
    {
        if (NULL == static::$userInstance) {
            static::$userInstance = new self();
            static::$userInstance->setConfig($config);
        }
        return static::$userInstance;
    }

    public function saveSessionInfo(array $sessionInfo, $platform = 'app')
    {
        $sessionName = Help::getRandomStr();
        if ('web' == $platform) {
            $prefix = self::WEB_SESSION_PREFIX;
            $max = self::ONLINE_COUNT;
        } elseif ('app' == $platform) {
            $prefix = self::APP_SESSION_PREFIX;
            $max = self::ONLINE_COUNT;
        } else {
            return false;
        }
        $sessionKey = $prefix . $sessionName;
        $userSessionKey = $prefix . $sessionInfo['uid'];
        static::$userInstance->LPUSH($userSessionKey, $sessionName);
        if (static::$userInstance->LLEN($userSessionKey) > self::ONLINE_COUNT) {
            $delSessionName = static::$userInstance->RPOP($userSessionKey);
            static::$userInstance->DEL($prefix . $delSessionName);
        }
        if (static::$userInstance->HMSET($sessionKey, $sessionInfo) && static::$userInstance->EXPIRE($sessionKey, self::SESSION_EXPIRE_TIME)) {
            return $sessionName;
        }
        return false;
    }

    public function getSessionInfo($sessionName, $platform = 'app')
    {
        if ('web' == $platform) {
            $prefix = self::WEB_SESSION_PREFIX;
        } elseif ('app' == $platform) {
            $prefix = self::APP_SESSION_PREFIX;
        } else {
            return false;
        }
        $sessionKey = $prefix . $sessionName;
        return static::$userInstance->HGET($sessionKey, 'uid');
    }
}