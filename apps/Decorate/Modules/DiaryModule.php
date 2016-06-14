<?php namespace Decorate\Modules;
/**
 * DiaryModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Models\Diary;
use Decorate\Models\File;

class DiaryModule extends BaseModule
{
    /**
     * 添加装修日志.
     * 
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add(array $data) {
        return Diary::create($data);
    }

    public function addFile($diaryId, $fileIdList) {
        $files = File::whereIn('id', $fileIdList)->select('id', 'mark_url', 'width', 'height')->get()->toArray();
        $nowTime = time();
        foreach ($files as $file) {
            $insertData[] = [
                'diary_id' => $diaryId,
                'file_id' => $file['id'],
                'file_url' => $file['mark_url'],
                'insert_time' => $nowTime,
                'modify_time' => $nowTime
            ];
        }
        if (!empty($insertData)) {
            return File::create($insertData);
        }
        return false;
    }

    public function getDiaryDetailById($diaryId) {
        $diarys = Diary::leftjoin('diary_file as df', 'df.diary_id', '=', 'diary.id')
            ->where('diary.id', $diaryId)
            ->select('diary.id', 'diary.title', 'diary.content', 'diary.decorate_progress', 'diary.decorate_label', 'df.file_id', 'df.file_url')
            ->get()->toArray();
        $diaryInfo = [];
        foreach ($diarys as $diary) {
            if (!isset($diaryInfo['id'])) {
                $diaryInfo = $diary;
                unset($diaryInfo['file_id']);
                unset($diaryInfo['file_url']);
            }
            if (!empty($diary['file_id']) && !empty($diary['file_url'])) {
                $diaryInfo['fileList'][] = ['id' => $diary['file_id'], 'file_url' => $diary['file_url']];
            }
        }
        return $diaryInfo;
    }

}
 