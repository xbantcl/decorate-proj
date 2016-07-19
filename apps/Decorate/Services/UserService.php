<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;

class UserService extends Service
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    public function test($request, $response, $args)
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
