<?php namespace Passport\Services;

use Illuminate\Support\Facades\Request;
use Respect\Validation\Validator as v;
use Passport\Enum\UserType;
use Passport\Utils\Help;
use Passport\Modules\UserModule;

class UserService extends Service
{
    /**
     * The userService class.
     *
     * @return json
     */
    public function login($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'account' => v::noWhitespace()->notEmpty(),
            'password' => v::noWhitespace()->notEmpty(),
        ]);
        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = $request->getParams();
        $data = UserModule::getInstance()->login($args['account'], $args['password'], $args['sys_p']);
        if (isset($data['code'])) {
            return Help::response($response, null, $data['code'], $data['message']);
        }
        return Help::response($response, $data);
    }

    /**
     * 用户注册.
     * 
     */
    public function register($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'user_type' => v::intVal()->between(UserType::ORD_USER, UserType::DESIGNER),
            'account' => v::noWhitespace()->notEmpty(),
            'password' => v::noWhitespace()->notEmpty(),
            'nick_name' => v::optional(v::noWhitespace()->notEmpty()),
            'sex' => v::optional(v::intVal()->between(1, 2)),
        ]);
        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = $request->getParams();
        $args['salt'] = Help::getSalt();
        $args['invite_code'] = Help::genInviteCode();
        $args['password'] = Help::encryptPassword($args['password'], $args['salt']);
        $data = UserModule::getInstance()->add($args);
        if (isset($data['code'])) {
            return Help::response($response, null, $data['code'], $data['message']);
        }
        return Help::response($response, $data);
    }

    /**
     * 获取用户信息.
     * 
     */
    public function getUserInfo($request, $response)
    {
        $args = Help::getParams($request, $this->uid);
        $userInfo = UserModule::getInstance()->getUserInfo($args['uid']);
        return Help::response($response, $userInfo);
    }

    public function updateUserInfo($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'cellphone' => v::optional(v::phone()),
            'nick_name' => v::optional(v::noWhitespace()->notEmpty()),
            'avatar' => v::optional(v::noWhitespace()->notEmpty()),
            'email' => v::optional(v::email()),
            'decorate_progress' => v::optional(v::intVal()),
            'decorate_style' => v::optional(v::intVal()),
            'decorate_area' => v::optional(v::numeric()),
            'sex' => v::optional(v::intVal()->between(1, 2)),
        ]);
        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        $ret = UserModule::getInstance()->updateUserInfo($args);
        if (isset($ret['code'])) {
            return Help::response($response, null, $ret['code'], $ret['message']);
        }
        return Help::response($response, $ret);
    }

    public function updatePassword($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'password' => v::noWhitespace()->notEmpty(),
        ]);
        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        $ret = UserModule::getInstance()->updatePassword($args);
        if (isset($ret['code'])) {
            return Help::response($response, null, $ret['code'], $ret['message']);
        }
        return Help::response($response, $ret);
    }
}
