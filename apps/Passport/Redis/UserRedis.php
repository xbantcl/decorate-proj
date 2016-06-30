<?php namespace Passport\Redis;
/**
 * User Redis
 */

use Passport\Utils\Help;
use Respect\Validation\Rules\NullType;
use Passport\Enum\ResCode;
use Passport\Enum\UserType;
use Passport\Models\OrdUser;
use Passport\Models\Seller;
use Passport\Models\Boss;
use Passport\Models\Worker;
use Passport\Models\Designer;

class UserRedis extends BaseRedis
{
    const WEB_SESSION_PREFIX = 'web_session#';
    const APP_SESSION_PREFIX = 'app_session#';
    const ONLINE_COUNT = 3;
    const SESSION_EXPIRE_TIME = 2592000;
    const USER_INVITE_CODE_KEY = 'invite_code';
    const USER_INFO_PREFIX = 'user#';

    private static $userInstance = NULL;

    public static $userFields = [
        'avatar' => 'string',
        'user_type' => 'int',
        'account' => 'string',
        'nick_name' => 'string',
        'cellphone' => 'string',
        'email' => 'string',
        'invite_code' => 'string',
        'sex' => 'int',
        'uid' => 'int',
        'modify_time' => 'int',
        'insert_time' => 'int',
        'decorate_progress' => 'int',
        'decorate_area' => 'float'
    ];

    /**
     * 实例化.
     * 
     * @param string $config
     * 
     * @return UserRedis
     */
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
            return Help::formatResponse(ResCode::INVALID_PLATFORM, '无效的平台');
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

    public function getAutoIncrementNum()
    {
        return static::$userInstance->INCR(self::USER_INVITE_CODE_KEY);
    }

    public function getUserInfoKey($uid) {
        return self::USER_INFO_PREFIX . $uid;
    }

    public function addUser(array $data)
    {
        if (empty($data['uid'])) {
            return false;
        }
        if (UserType::ORD_USER == $data['user_type']) {
            $data = array_intersect_key($data, array_merge(OrdUser::$rules, static::$userFields));
        } elseif (UserType::SELLER == $data['user_type']) {
            $data = array_intersect_key($data, array_merge(Seller::$rules, static::$userFields));
        } elseif (UserType::BOSS == $data['user_type']) {
            $data = array_intersect_key($data, array_merge(Boss::$rules, static::$userFields));
        } elseif (UserType::WORKER == $data['user_type']) {
            $data = array_intersect_key($data, array_merge(Worker::$rules, static::$userFields));
        } elseif (UserType::DESIGNER == $data['user_type']) {
            $data = array_intersect_key($data, array_merge(Designer::$rules, static::$userFields));
        } else {
            return false;
        }
        return static::$userInstance->HMSET($this->getUserInfoKey($data['uid']), $data);
    }

    public function getUserInfo($uid)
    {
        $userInfo = static::$userInstance->HGETALL($this->getUserInfoKey($uid));
        return Help::casts($userInfo, static::$userFields);
    }

    public function updateUserInfo(array $data)
    {
        return $this->addUser($data);
    }

    public function getUserInfoByBatch(array $uids, array $fields = [])
    {
        if (empty($uids)) {
            return [];
        }
        $usersInfo = [];
        $ret = static::$userInstance->pipeline(function ($pipe) use ($uids, $fields) {
            foreach ($uids as $uid) {
                if ($fields) {
                    $pipe->HMGET($this->getUserInfoKey($uid), $fields);
                } else {
                    $pipe->HGETALL($this->getUserInfoKey($uid));
                }
            }
        });

        foreach ($ret as $index => $item) {
            $temp = $item;
            if ($fields) {
                $item = [];
                foreach ($fields as $key => $field) {
                    $item[$field] = $temp[$key];
                }
            }
            if (0 == $item['uid']) {
                $item['avatar'] = '';
                $item['nick_name'] = '未知用户';
            }
            $item = Help::casts($item, static::$userFields);
            $usersInfo[$uids[$index]] = $item;
        }
        return $usersInfo;
    }
}