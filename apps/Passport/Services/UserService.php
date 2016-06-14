<?php namespace Passport\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Factory as Validator;
use Symfony\Component\Translation\Translator;

class UserService
{
    /**
     * The userService class.
     *
     * @return json
     */
    public function login($request, $response)
    {
        $validator = new Validator(new Translator());
        $validator = $validator->make($request->getParams(), [
            'username' => 'required|string',
            'password' => 'required|string',
            'sys_p' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            $messages = $validator->messages()->toArray();
            foreach ($messages as $message) {
                return Response::generate(Response::PARAM_ERROR, null, implode(',',$message));
            }
        }
        return $response->write(json_encode(
            [
                'status' => 200,
                'error' => '',
                'datas' => $request->getParsedBody()
            ]
        ));
    }

    public function register($request, $response)
    {
        $args = $request->getParams();
        
    }
}
