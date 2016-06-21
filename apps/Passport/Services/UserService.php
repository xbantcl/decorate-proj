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
            'user_type' => v::numeric()->between(UserType::ORD_USER, UserType::WORKER),
            'account' => v::noWhitespace()->notEmpty(),
            'password' => v::noWhitespace()->notEmpty(),
        ]);
        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = $request->getParams();
        $args['salt'] = Help::getSalt();
        $args['password'] = Help::encryptPassword($args['password'], $args['salt']);
        $data = UserModule::getInstance()->add($args);
        if (isset($data['code'])) {
            return Help::response($response, null, $data['code'], $data['message']);
        }
        return Help::response($response, $data);
    }
}
