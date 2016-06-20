<?php namespace Passport\Services;

use Illuminate\Support\Facades\Request;
use Respect\Validation\Validator as v;
use Passport\Enum\UserType;
use Passport\Util\Help;
use Passport\Models\User;
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
        //var_dump(v::alnum()->noWhitespace()->length(1, 10));exit;
        $args = $request->getParams();
        return $response->write(json_encode(
            [
                'status' => 200,
                'error' => '',
                'datas' => $args
            ]
        ));
    }

    public function register($request, $response)
    {
        $validation = $this->validation->validate($request, [
//                'user_type' => v::numeric()->between(UserType::ORD_USER, UserType::WORKER),
  //              'account' => v::noWhitespace()->notEmpty(),
                'password' => v::noWhitespace(),
        ]);
        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = $request->getParams();
        $args['salt'] = Help::getSalt();
        $args['password'] = Help::encryptPassword($args['password'], $args['salt']);
        $data = array_intersect_key($args, user::$rules);
        UserModule::getInstance()->add($data);
    }
}
