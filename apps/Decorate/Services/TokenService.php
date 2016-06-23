<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;
use Decorate\Utils\Help;
use Decorate\Enum\ResCode;
use Qiniu\Auth;
use Respect\Validation\Validator as v;

class TokenService extends Service
{
    /**
     * 获取七牛上传文件token.
     *
     */
    public function getUploadFileToken($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'bucket' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        $configs = Help::config('qiniu');
        $bucket = strtolower($args['bucket']);
        if (!isset($configs['bucket']['pub'][$bucket])) {
            Help::response($response, null, ResCode::BUCKET_NOT_EXIST, '上传bucket不存在');
        }
        if (!isset($configs['accessKey']) || !isset($configs['secretKey'])) {
            Help::response($response, null, ResCode::UPLOAD_KEY_NOT_EXIST, '上传密钥不存在');
        }
        $policy = ['returnBody'=>'{"key":$(key),"w":$(imageInfo.height),"h":$(imageInfo.height)}'];
        /*
        $fops = 'watermark/1/image/' . $logoUrl;
        $fops .= '|saveas/' . $bucketPub;
        $policy['persistentOps'] = $fops;
        // 指定独享队列
        if ($this->channels) {
            $index = time() % 4;
            $channel = $this->channels[$index];
            $policy['persistentPipeline'] = $channel;
        }
        */
        $auth = new Auth($configs['accessKey'], $configs['secretKey']);
        return $auth->uploadToken($bucket, null, 3600, $policy);
    }
}
