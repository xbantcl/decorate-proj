<?php namespace Decorate\Modules;
/**
 * DiaryModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Models\Diary;
use Decorate\Models\File;
use Decorate\Models\DiaryFile;
use Passport\Modules\UserModule;

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
        $data = array_intersect_key($data, Diary::$rules);
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

    public function getDiaryDetailById($diaryId)
    {
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

    public function delDiaryById($diaryId, $uid)
    {
        $ret = Diary::where('id', $diaryId)->where('uid', $uid)->delete();
        if ($ret) {
            DiaryFile::where('diary_id', $diaryId)->delete();
        }
    }

    public function getDiaryList($start = 0, $limit = 15)
    {
        $query = Diary::leftjoin('diary_file as df', 'df.diary_id', '=', 'diary.id')
            ->select('diary.title', 'diary.decorate_progress', 'diary.label_id', 'diary.content', 'diary.insert_time', 'df.file_id', 'df.file_url')
            ->orderBy('diary.id', 'desc')
            ->groupBy('uid');
        if ($start > 0) {
            $query = $query->where('diary.id', '<', $start);
        }
        $diaries = $query->take($limit + 1)->get()->toArray();
        if (empty($diaries)) {
            return ['start' => 0, 'more' => 0, 'data' => []];
        }
        $uids = [];
        $diaryList = [];
        $count = 0;
        $more = 0;
        foreach ($diaries as $diary) {
            if (!in_array($diary['uid'], $uids)) {
                $uids[] = $diary['uid'];
            }
            if (!isset($diaryList[$diary['id']])) {
                $temp = $diary;
                unset($temp['file_id']);
                unset($temp['file_url']);
                $diaryList[$diary['id']] = $temp;
                $count ++;
            }
            if (!empty($diary['file_id']) && !empty($diary['file_url'])) {
                $diaryList[$diary['id']]['fileList'] = ['id' => $diary['file_id'], 'file_url' => $diary['file_url']];
            }
        }
        $usersInfo = UserModule::getInstance()->getUserInfoByBatch($uids, ['uid', 'avatar', 'nick_name']);
        foreach ($diaryList as &$diary) {
            if (isset($usersInfo[$diary['uid']])) {
                $diary['user'] = $usersInfo[$diary['uid']];
            }
        }
        if ($count > $limit) {
            $more = 1;
            array_pop($diaryList);
        }
        $start = end($diaryList)['id'];
        return ['start' => $start, 'more' => $more, 'data' => $diaryList];
    }
}