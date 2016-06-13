<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;
use Decorate\Modules\DiaryModule;

class DiaryService
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    public function add($request, $response, $args)
    {
        $args = $request->getParams();
        $ret = DiaryModule::getInstance()->add($args);
        return $response->write(json_encode(
            [
                'status' => 200,
                'error' => '',
                'datas' => $ret
            ]
        ));
    }
}
