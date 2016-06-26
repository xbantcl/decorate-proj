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
        if (true === ($ret = $this->checkUser($args['account']))) {
            return ResCode::formatError(ResCode::ACCOUNT_EXIST);
        }
        $args = array_merge($args, $ret);
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
                $args['decorate_progress'] = isset($args['decorate_progress']) ? $args['decorate_progress'] : 1; // 装修面积.
                $args['districts'] = '';
                $data = array_intersect_key($args, OrdUser::$rules);
                $userObj = OrdUser::create($data);
                $ret['dec_fund'] = Help::calcDecFund($userObj->dec_fund);
                $ret['decorate_style'] = DecorateType::getDecorateStyleName($userObj->decorate_style);
                $ret['decorate_type'] = HouseType::getHouseStyleName($userObj->decorate_type);
                $ret['districts'] = $userObj->districts;
                $ret['decorate_area'] = $userObj->decorate_area;
                $ret['decorate_progress'] = DecorateType::getDecorateStatus($userObj->decorate_progress);
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
                return ResCode::formatError(ResCode::INVALID_USER_TYPE);
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
            return ResCode::formatError(ResCode::SYSTEM_ERROR);
        }
        $status = UserRedis::getInstance()->addUser(array_merge($args, $userObj->toArray()));
        if (!$status) {
            return ResCode::formatError(ResCode::SYSTEM_ERROR);
        }
        DB::commit();
        return array_merge($ret, ['uid' => $user->id, 'user_type' => $user->user_type, 'avatar' => $user->avatar, 'sex' => $user->sex, 'sess' => $sessionName]);
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
            return ResCode::formatError(ResCode::ACCOUNT_NOT_EXIST);
        }

        if (Help::encryptPassword($password, $user->salt) != $user->password) {
            return ResCode::formatError(ResCode::PASSWORD_ERROR);
        }
        $sessionName = UserRedis::getInstance()->saveSessionInfo(['uid' => $user->id], $platform);
        if (!$sessionName) {
            return ResCode::formatError(ResCode::SYSTEM_ERROR);
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
        $param['account'] = '';
        if (Help::isPhone($account)) {
            $query = User::where('cellphone', $account);
            $param['cellphone'] = $account;
        } elseif (Help::isEmail($account)) {
            $query = User::where('email', $email);
            $param['email'] = $account;
        } else {
            $query = User::where('account', $account);
            $param['account'] = $account;
        }
        $user = $query->select('id')->first();
        if (!$user instanceof User) {
            return $param;
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
            return ResCode::formatError(ResCode::INVALID_SESSION);
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
            $userInfo['decorate_progress'] = DecorateType::getDecorateStatus($userInfo['decorate_progress']);
        }
        return $userInfo;
    }

    /**
     * 更新用户信息.
     * 
     * @param array $data
     * 
     * @return boolean
     */
    public function updateUserInfo(array $data)
    {
        if (empty($data['uid'])) {
            return false;
        }

        $user = User::select('id', 'user_type')->find($data['uid']);
        if (! $user instanceof User) {
            return ResCode::formatError(ResCode::ACCOUNT_NOT_EXIST);
        }
        DB::beginTransaction();
        try {
            User::where('id', $data['uid'])->update(array_intersect_key($data, User::$rules));
            if (UserType::ORD_USER == $user->user_type) {
                OrdUser::where('uid', $data['uid'])->update(array_intersect_key($data, OrdUser::$rules));
            } elseif (UserType::BOSS == $user->user_type) {
                Boss::where('uid', $data['uid'])->update(array_intersect_key($data, Boss::$rules));
            } elseif (UserType::SELLER == $user->user_type) {
                Seller::where('uid', $data['uid'])->update(array_intersect_key($data, Seller::$rules));
            } elseif (UserType::WORKER == $user->user_type) {
                Worker::where('uid', $data['uid'])->update(array_intersect_key($data, Worker::$rules));
            } elseif (UserType::DESIGNER == $user->user_type) {
                Designer::where('uid', $data['uid'])->update(array_intersect_key($data, Designer::$rules));
            } else {
                throw new \Exception(null, ResCode::INVALID_USER_TYPE);
            }
        } catch (\Exception $e) {
            return ResCode::formatError($e->getCode());
        }
        $ret = UserRedis::getInstance()->updateUserInfo($data);
        if (!$ret) {
            return ResCode::formatError(ResCode::UPDATE_USER_INFO_FAILED);
        }
        DB::commit();
        return true;
    }
}
 