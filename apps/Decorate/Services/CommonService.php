<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;

class CommonService
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    public function addCollection($request, $response, $args)
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
