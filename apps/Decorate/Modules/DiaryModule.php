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
use Decorate\Redis\DiaryRedis;

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
            $diaryData['insert_time'] = $data['add_time'];
            $diary = Diary::create($diaryData);
            if (!empty($data['fileList'])) {
                $fileList = FileModule::getInstance()->add($diary->id, $data['fileList'], FileType::DIARY_FILE, 'decorate-pic');
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
            return '未知标签';
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
        $diaries = $query->take(($limit + 1) * 9)->get()->toArray();

        if (empty($diaries)) {
            return ['start' => 0, 'more' => 0, 'data' => (object)[]];
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
            if ($count > $limit) {
                break;
            }
            // $diaryIds[] = $diary['id'];
            if (!isset($diaryList[$diary['id']])) {
                $temp = $diary;
                unset($temp['file_id']);
                unset($temp['file_url']);
                $diaryList[$diary['id']] = $temp;
                $diaryList[$diary['id']]['counter'] = DiaryRedis::getInstance()->getCounter($diary['id']);
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
            $diaryList = array_slice($diaryList, 0, $limit);
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
                $fileList = FileModule::getInstance()->add($diaryComment->id, $data['fileList'], FileType::DIARY_COMMENT_FILE, 'decorate-pic');
                if (isset($fileList['code'])) {
                    DB::rollback();
                    return $fileList;
                }
                $diaryComment->fileList = $fileList;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ResCode::formatError(ResCode::ADD_DIARY_FAILED);
        }
        DB::commit();
        DiaryRedis::getInstance()->comment($data['diary_id']);
        return $diaryComment;
    }

    public function getUserDiaryList($uid)
    {
        $diaries = Diary::leftjoin('diary_file as df', 'df.diary_id', '=', 'diary.id')
            ->select('diary.id', 'diary.title', 'diary.uid', 'diary.decorate_progress', 'diary.label_id', 'diary.content', 'diary.insert_time', 'df.file_id', 'df.file_url')
            ->orderBy('diary.id', 'asc')
            ->where('diary.uid', $uid)
            ->get()->toArray();
        $diarieList = array_values($this->formatDiaryData($diaries));
        $data = array_map(function ($diary) {
            $diary['counter'] = DiaryRedis::getInstance()->getCounter($diary['id']);
            return $diary;
        }, $diarieList);
        return ['data' => $data];
    }

    public function formatDiaryData(array $diaries)
    {
        $uids = [];
        $diaryList = [];
        foreach ($diaries as $diary) {
            if (!in_array($diary['uid'], $uids)) {
                $uids[] = $diary['uid'];
            }
            if (isset($diary['target_uid']) && $diary['target_uid'] > 0 && !in_array($diary['target_uid'], $uids)) {
                $uids[] = $diary['target_uid'];
            }
            if (!isset($diaryList[$diary['id']])) {
                $temp = $diary;
                unset($temp['file_id']);
                unset($temp['file_url']);
                $diaryList[$diary['id']] = $temp;
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
            if (isset($diary['target_uid']) && isset($usersInfo[$diary['target_uid']])) {
                $diary['puser'] = $usersInfo[$diary['target_uid']];
            }
        }
        return $diaryList;
    }

    public function getDiaryCommentList($diaryId, $start, $limit)
    {
        $query = DiaryComment::leftjoin('diary_comment_file as dcf', 'dcf.diary_comment_id', '=', 'diary_comment.id')
            ->select('diary_comment.id', 'diary_comment.uid', 'diary_comment.content', 'diary_comment.parent_id', 'diary_comment.target_uid', 'dcf.file_id', 'dcf.file_url',
                'diary_comment.insert_time') // 'dc.id as r_id', 'dc.uid as r_uid', 'dc.content as r_content', 'dc.parent_id as r_parent_id', 'dc.insert_time as r_insert_time')
            //->leftjoin('diary_comment as dc', 'dc.parent_id', '=', 'diary_comment.id')
            ->where('diary_comment.diary_id', $diaryId)
            ->where('diary_comment.parent_id', 0)
            ->orderBy('diary_comment.id', 'desc');
        if ($start > 0) {
            $query = $query->where('diary_comment.id', '<', $start);
        }
        $diaryComments = $query->take(($limit + 1) * 9)->get()->toArray();

        $more = 0;
        if (empty($diaryComments)) {
            return ['start' => $start, 'more' => $more, 'data' => (object)[]];
        }
        $diaryComments = $this->formatDiaryData($diaryComments);

        if (count($diaryComments) > $limit) {
            $more = 1;
            $diaryComments = array_slice($diaryComments, 0, $limit);
        }
        $diaryComments = array_values($diaryComments);
        $start = end($diaryComments)['id'];
        // 获取二级评论
        $commentIds = [];
        $replyComments = [];
        foreach ($diaryComments as $diaryComment) {
            $commentIds[] = $diaryComment['id'];
        }
        if ($commentIds) {
            $replyComments = DiaryComment::leftjoin('diary_comment_file as dcf', 'dcf.diary_comment_id', '=', 'diary_comment.id')
                ->select('diary_comment.id', 'diary_comment.uid', 'diary_comment.content', 'diary_comment.parent_id', 'diary_comment.target_uid', 'dcf.file_id', 'dcf.file_url',
                    'diary_comment.insert_time')
                ->whereIn('diary_comment.parent_id', $commentIds)
                ->get()->toArray();
            $replyComments = $this->formatDiaryData($replyComments);
        }
        $diaryCommentList = array_map(function($item) use ($replyComments) {
            foreach ($replyComments as $replyComment) {
                if ($item['id'] == $replyComment['parent_id']) {
                    $item['reply_comment'][] = $replyComment;
                }
            }
            return $item;
        }, $diaryComments);

        return ['start' => $start, 'more' => $more, 'data' => $diaryCommentList];
    }

    public function getDiaryByIds(array $diaryIds)
    {
        if (!$diaryIds) {
            return [];
        }
        $diaries = Diary::leftjoin('diary_file as df', 'df.diary_id', '=', 'diary.id')
            ->select('diary.id', 'diary.title', 'diary.uid', 'diary.decorate_progress', 'diary.label_id', 'diary.content', 'diary.insert_time', 'df.file_id', 'df.file_url')
            ->orderBy('diary.id', 'asc')
            ->whereIn('diary.id', $diaryIds)
            ->get()->toArray();
        $diarieList = array_values($this->formatDiaryData($diaries));
        $data = array_map(function ($diary) {
            $diary['counter'] = DiaryRedis::getInstance()->getCounter($diary['id']);
            return $diary;
        }, $diarieList);
        return $data;
    }
}
