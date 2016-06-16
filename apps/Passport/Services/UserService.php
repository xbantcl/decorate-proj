<?php namespace Passport\Services;

use Illuminate\Support\Facades\Request;
use Respect\Validation\Validator as v;

class UserService
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
        $args = $request->getParams();
        
    }
}
