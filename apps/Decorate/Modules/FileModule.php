<?php namespace Decorate\Modules;
/**
 * FModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Models\File;
use Decorate\Models\DiaryFile;
use Passport\Modules\UserModule;
use Decorate\Utils\Help;
use Decorate\Enum\ResCode;
use Decorate\Enum\FileType;
use Decorate\Models\DiaryCommentFile;
use Decorate\Models\DiscussFile;
use Decorate\Models\DiscussCommentFile;
use Decorate\Models\WorksFile;


class FileModule extends BaseModule
{
    /**
     * 添加文件.
     * 
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add($resId, array $fileList, $type, $bucket = '') {
        if (!$bucket) {
            return ResCode::formatError(ResCode::FILE_BUCKET_NOT_EXIST);
        } else {
            $configs = Help::config('qiniu')['bucket']['pub'];
            $resDomin = isset($configs[$bucket]) ? $configs[$bucket] : '';
            if (!$resDomin) {
                return ResCode::formatError(ResCode::FILE_BUCKET_NOT_EXIST);
            }
        }
        $uuid = Help::getUuid();
        $nowTime = time();
        try {
            $insertData = [];
            foreach ($fileList as $file) {
                $insertData[] = [
                    'intr' => '',
                    'url' => $resDomin . $file['url'],
                    'mark_url' => $resDomin . $file['url'],
                    'size' => isset($file['size']) ? $file['size'] : 0,
                    'width' => isset($file['width']) ? $file['width'] : 0,
                    'height' => isset($file['height']) ? $file['height'] : 0,
                    'duration' => isset($file['duration']) ? $file['duration'] : 0,
                    'uuid' => $uuid,
                    'insert_time' => $nowTime,
                    'modify_time' => $nowTime
                ];
            }
            File::insert($insertData);
            $files = File::select('id', 'mark_url')->where('uuid', $uuid)->get()->toArray();
 
            if (!empty($files) && FileType::DIARY_FILE == $type) {
                $data = $this->formatByRule($resId, $files, DiaryFile::$rules);
                if (!empty($data['insertData'])) {
                   DiaryFile::insert($data['insertData']);
                }
            } elseif (!empty($files) && FileType::DIARY_COMMENT_FILE == $type) {
                $data = $this->formatByRule($resId, $files, DiaryCommentFile::$rules);
                if (!empty($data['insertData'])) {
                    DiaryCommentFile::insert($data['insertData']);
                }
            } elseif (!empty($files) && FileType::DISCUSS_FILE == $type) {
                $data = $this->formatByRule($resId, $files, DiscussFile::$rules);
                if (!empty($data['insertData'])) {
                    DiscussFile::insert($data['insertData']);
                }
            } elseif (!empty($files) && FileType::DISCUSS_COMMENT_FILE == $type) {
                $data = $this->formatByRule($resId, $files, DiscussCommentFile::$rules);
                if (!empty($data['insertData'])) {
                    DiscussCommentFile::insert($data['insertData']);
                }
            } elseif (!empty($files) && FileType::WORKS_FILE == $type) {
                $data = $this->formatByRule($resId, $files, WorksFile::$rules);
                if (!empty($data['insertData'])) {
                    WorksFile::insert($data['insertData']);
                }
            }
        } catch (\Exception $e) {
            return ResCode::formatError(ResCode::ADD_DIARY_FAILED);
        }
        $fileList = isset($data['fileList']) ? $data['fileList'] : [];
        return $fileList;
    }

    public function formatByRule($resId, $data, $rule)
    {
        $fileList = [];
        $insertData = [];
        $nowTime = time();
        foreach ($data as $item) {
            $data = [
                'diary_id' => $resId,
                'diary_comment_id' => $resId,
                'discuss_id' => $resId,
                'discuss_comment_id' => $resId,
                'works_id' => $resId,
                'file_id' => $item['id'],
                'file_url' => $item['mark_url'],
                'insert_time' => $nowTime,
                'modify_time' => $nowTime
            ];
            $insertData[] = array_intersect_key($data, $rule);
            $fileList[] = [
                'id' => $item['id'],
                'url' => $item['mark_url']
            ];
        }
        return ['insertData' => $insertData, 'fileList' => $fileList];
    }
}