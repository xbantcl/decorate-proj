<?php namespace Passport\Modules;
/**
 * UserModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Passport\Models\User;
use Passport\Enum\UserType;
use Illuminate\Database\Capsule\Manager as DB;
use Passport\Models\OrdUser;
use Passport\Models\Seller;
use Passport\Models\Boss;
use Passport\Models\Worker;
use Passport\Utils\Help;
use Passport\Enum\ResCode;
use Passport\Redis\UserRedis;

class UserModule extends BaseModule
{
    /**
     * 添加用户.
     * 
     * @param array $args
     * 
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add(array $args) {
        if ($this->checkUser($args['account'])) {
            return Help::formatResponse(ResCode::ACCOUNT_EXIST, '帐号已经存在');
        }
        try {
            DB::beginTransaction();
            $data = array_intersect_key($args, User::$rules);
            $user = User::create($data);
            $args['uid'] = $user->id;
            if (UserType::ORD_USER == $data['user_type']) {
                $data = array_intersect_key($args, OrdUser::$rules);
                OrdUser::create($data);
            } elseif (UserType::SELLER == $data['user_type']) {
                $data = array_intersect_key($args, Seller::$rules);
                Seller::create($data);
            } elseif (UserType::BOSS == $data['user_type']) {
                $data = array_intersect_key($args, Boss::$rules);
                Boss::create($data);
            } elseif (UserType::WORKER == $data['user_type']) {
                $data = array_intersect_key($args, Worker::$rules);
                Worker::create($data);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        $sessionName = UserRedis::getInstance()->saveSessionInfo(['uid' => $user->id], $args['sys_p']);
        if(is_array($sessionName)) {
            return $sessionName;
        }
        if (!$sessionName) {
            return Help::formatResponse(ResCode::SYSTEM_ERROR, '系统错误');
        }
        DB::commit();
        return [
            'uid' => $user->id,
            'user_type' => $user->user_type,
            'sess' => $sessionName
        ];
    }

    /**
     * 用户登录.
     * 
     * @param string $account
     * @param string $password
     * @param integer $userType
     * @param string $platform
     * 
     * @return mixed
     */
    public function login($account, $password, $userType = 1, $platform = 'app')
    {
        if (Help::isPhone($account)) {
            $query = User::where('cellphone', $account);
        } elseif (Help::isEmail($account)) {
            $query = User::where('email', $email);
        } else {
            $query = User::where('account', $account);
        }
        $user = $query->select('id', 'password', 'salt', 'user_type')->first();
        if (!$user instanceof User) {
            return Help::formatResponse(ResCode::ACCOUNT_NOT_EXIST, '帐号不存在');
        }

        if (Help::encryptPassword($password, $user->salt) != $user->password) {
            return Help::formatResponse(ResCode::PASSWORD_ERROR, '密码错误');
        }
        $sessionName = UserRedis::getInstance()->saveSessionInfo(['uid' => $user->id], $platform);
        if (!$sessionName) {
            return Help::formatResponse(ResCode::SYSTEM_ERROR, '系统错误');
        }
        return [
            'uid' => $user->id,
            'user_type' => $user->user_type,
            'sess' => $sessionName
        ];
    }

    /**
     * 帐号查询.
     * 
     * @param string $account
     * 
     * @return boolean
     */
    public function checkUser($account)
    {
        if (Help::isPhone($account)) {
            $query = User::where('cellphone', $account);
        } elseif (Help::isEmail($account)) {
            $query = User::where('email', $email);
        } else {
            $query = User::where('account', $account);
        }
        $user = $query->select('id')->first();
        if (!$user instanceof User) {
            return false;
        }
        return true;
    }

    /**
     * 验证登录状态.
     * 
     * @param string $sessionName
     */
    public function checkLogin($sessionName, $platform = 'app') {
        $uid = UserRedis::getInstance()->getSessionInfo($sessionName, $platform);
        if (!$uid) {
            return Help::formatResponse(ResCode::INVALID_SESSION, '无效的sesion,请重新登录');
        }
        return $uid;
    }
}
 