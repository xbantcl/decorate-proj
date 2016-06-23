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
use Passport\Models\Designer;
use Passport\Enum\DecorateType;
use Passport\Enum\HouseType;

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
        $ret = [];
        try {
            DB::beginTransaction();
            $args['sex'] = isset($args['sex']) ? $args['sex'] : 1;
            $args['avatar'] = UserType::$avatar[$args['sex']];
            $data = array_intersect_key($args, User::$rules);
            $user = User::create($data);
            $args['uid'] = $user->id;
            if (UserType::ORD_USER == $data['user_type']) {
                $args['dec_fund'] = isset($args['dec_fund']) ? $args['dec_fund'] : 0; // 装修基金.
                $args['decorate_style'] = isset($args['decorate_style']) ? $args['decorate_style'] : 0; // 装修风格.
                $args['decorate_type'] = isset($args['decorate_type']) ? $args['decorate_type'] : 0; // 装修类型.
                $args['decorate_area'] = isset($args['decorate_area']) ? $args['decorate_area'] : 0; // 装修面积.
                $args['districts'] = '';
                $data = array_intersect_key($args, OrdUser::$rules);
                $userObj = OrdUser::create($data);
                $ret['dec_fund'] = Help::calcDecFund($userObj->dec_fund);
                $ret['decorate_style'] = DecorateType::getDecorateStyleName($userObj->decorate_style);
                $ret['decorate_type'] = HouseType::getHouseStyleName($userObj->decorate_type);
                $ret['districts'] = $userObj->districts;
                $ret['decorate_area'] = $userObj->decorate_area;
            } elseif (UserType::SELLER == $data['user_type']) {
                $data = array_intersect_key($args, Seller::$rules);
                $userObj = Seller::create($data);
            } elseif (UserType::BOSS == $data['user_type']) {
                $data = array_intersect_key($args, Boss::$rules);
                $userObj = Boss::create($data);
            } elseif (UserType::WORKER == $data['user_type']) {
                $data = array_intersect_key($args, Worker::$rules);
                $userObj = Worker::create($data);
            } elseif (UserType::DESIGNER == $data['user_type']) {
                $data = array_intersect_key($args, Designer::$rules);
                $userObj = Designer::create($data);
            } else {
                return Help::formatResponse(ResCode::INVALID_USER_TYPE, '无效的用户类型');
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        $sessionName = UserRedis::getInstance()->saveSessionInfo(['uid' => $user->id], $args['cli_p']);
        if(is_array($sessionName)) {
            return $sessionName;
        }
        if (!$sessionName) {
            return Help::formatResponse(ResCode::SYSTEM_ERROR, '系统错误');
        }
        $status = UserRedis::getInstance()->addUser(array_merge($args, $userObj->toArray()));
        if (!$status) {
            return Help::formatResponse(ResCode::SYSTEM_ERROR, '系统错误');
        }
        DB::commit();
        return array_merge($ret, ['uid' => $user->uid, 'user_type' => $user->user_type, 'avatar' => $user->avatar, 'sex' => $user->sex, 'sess' => $sessionName]);
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
        return array_merge($this->getUserInfo($user->id), ['sess' => $sessionName]);
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

    public function getUserInfo($uid)
    {
        $userInfo = UserRedis::getInstance()->getUserInfo($uid);
        if (!$userInfo) {
            $user = User::select('id', 'avatar', 'sex', 'nick_name', 'invite_code', 'account', 'user_type')->where('id', $uid)->first();
            if (!$user instanceof User) {
                return false;
            }
            if (UserType::ORD_USER == $user->user_type) {
                
            }
        }
        if (UserType::ORD_USER == $userInfo['user_type']) {
            $userInfo['dec_fund'] = Help::calcDecFund($userInfo['dec_fund'] );
            $userInfo['decorate_style'] = DecorateType::getDecorateStyleName($userInfo['decorate_style']);
            $userInfo['decorate_type'] = HouseType::getHouseStyleName($$userInfo['decorate_type']);
        }
        return $userInfo;
    }
}
 