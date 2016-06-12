<?php namespace Passport\Services;

use Illuminate\Support\Facades\Request;

class UserService
{
    /**
     * The userService class.
     *
     * @return json
     */
    public function login($request, $response, $args)
    {
        return $response->write(json_encode(
            [
                'status' => 200,
                'error' => '',
                'datas' => $request->getParsedBody()
            ]
        ));
    }
}
