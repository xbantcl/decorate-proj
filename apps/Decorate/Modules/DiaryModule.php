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
use Decorate\Utils\Help;
use Decorate\Enum\ResCode;
use Illuminate\Database\Capsule\Manager as DB;
use Decorate\Enum\FileType;
use Decorate\Models\DecorateLabel;
use Decorate\Models\DiaryComment;

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
        DB::beginTransaction();
        try {
            $diaryData = array_intersect_key($data, Diary::$rules);
            $diary = Diary::create($diaryData);
            if (!empty($data['fileList'])) {
                $fileList = FileModule::getInstance()->add($diary->id, $data['fileList'], FileType::DIARY_FILE);
                if (isset($fileList['code'])) {
                    DB::rollback();
                    return $fileList;
                }
                $diary->fileList = $fileList;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ResCode::formatError(ResCode::ADD_DIARY_FAILED);
        }
        DB::commit();
        $diary->label_name = $this->getLabelNameById($diary->label_id);
        return $diary;
    }

    public function getLabelNameById($labelId) {
        $label = DecorateLabel::select('name')->find($labelId);
        if (!$label instanceof DecorateLabel) {
            return '未知道';
        }
        return $label->name;
    }

    public function formatLabels($labelList, $parentId = 0) {
        $labelTree = [];
        $parentIds = [];
        if (empty($labelList) && 0 == $parentId) {
            return $labelTree;
        }
        foreach ($labelList as $index => $label) {
            if ($parentId == $label['parentId']) {
                $labelId = $label['id'];
                unset($label['parentId']);
                $labelTree[$labelId] = $label;
                $parentIds[] = $labelId;
                unset($labelList[$index]);
            }
        }
        if (empty($labelTree) || empty($labelList)) {
            return array_values($labelTree);
        }
        foreach ($parentIds as $parentId) {
            $value = $this->formatLabels($labelList, $parentId);
            if ($value) {
                $labelTree[$parentId]['childList'] = $value;
            }
        }
        return array_values($labelTree);
    }

    public function getLabelTree()
    {
        $labelList = DecorateLabel::select('id', 'name', 'parentId')->get()->toArray();
        return $this->formatLabels($labelList);
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
            ->leftjoin('decorate_label as dl', 'diary.label_id', '=', 'dl.id')
            ->where('diary.id', $diaryId)
            ->select('diary.id', 'diary.title', 'diary.content', 'diary.decorate_progress', 'dl.name as label_name', 'df.file_id', 'df.file_url')
            ->get()->toArray();
        $diaryInfo = [];
        foreach ($diarys as $diary) {
            if (!isset($diaryInfo['id'])) {
                $diaryInfo = $diary;
                unset($diaryInfo['file_id']);
                unset($diaryInfo['file_url']);
            }
            if (!empty($diary['file_id']) && !empty($diary['file_url'])) {
                $diaryInfo['fileList'][] = ['id' => $diary['file_id'], 'url' => $diary['file_url']];
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
            ->select('diary.id', 'diary.title', 'diary.uid', 'diary.decorate_progress', 'diary.label_id', 'diary.content', 'diary.insert_time', 'df.file_id', 'df.file_url')
            ->orderBy('diary.id', 'desc');
            // ->groupBy('diary.uid');
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
        // $diaryIds = [];
        foreach ($diaries as $diary) {
            if (!in_array($diary['uid'], $uids)) {
                $uids[] = $diary['uid'];
            }
            // $diaryIds[] = $diary['id'];
            if (!isset($diaryList[$diary['id']])) {
                $temp = $diary;
                unset($temp['file_id']);
                unset($temp['file_url']);
                $diaryList[$diary['id']] = $temp;
                $count ++;
            }
            if (!empty($diary['file_id']) && !empty($diary['file_url'])) {
                $diaryList[$diary['id']]['fileList'][] = ['id' => $diary['file_id'], 'url' => $diary['file_url']];
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
        return ['start' => $start, 'more' => $more, 'data' => array_values($diaryList)];
    }

    public function commentDiary(array $data)
    {
        DB::beginTransaction();
        try {
            $diaryCommentData = array_intersect_key($data, DiaryComment::$rules);
            $diaryComment = DiaryComment::create($diaryCommentData);
            if (!empty($data['fileList'])) {
                $fileList = FileModule::getInstance()->add($diaryComment->id, $data['fileList'], FileType::DIARY_COMMENT_FILE);
                if (isset($fileList['code'])) {
                    DB::rollback();
                    return $fileList;
                }
                $diary->fileList = $fileList;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ResCode::formatError(ResCode::ADD_DIARY_FAILED);
        }
        DB::commit();
        return $diaryComment;
    }
}