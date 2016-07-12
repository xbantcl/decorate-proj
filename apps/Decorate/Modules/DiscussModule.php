<?php namespace Decorate\Modules;
/**
 * DiscussModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Models\Discuss;
use Decorate\Models\File;
use Decorate\Models\DiscussFile;
use Passport\Modules\UserModule;
use Decorate\Utils\Help;
use Decorate\Enum\ResCode;
use Illuminate\Database\Capsule\Manager as DB;
use Decorate\Enum\FileType;
use Decorate\Models\DecorateLabel;
use Decorate\Models\DiscussComment;
use Decorate\Redis\DiscussRedis;

class DiscussModule extends BaseModule
{
    /**
     * 添加讨论问题.
     * 
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add(array $data) {
        DB::beginTransaction();
        try {
            $discussData = array_intersect_key($data, Discuss::$rules);
            $discuss = Discuss::create($discussData);
            if (!empty($data['fileList'])) {
                $fileList = FileModule::getInstance()->add($discuss->id, $data['fileList'], FileType::DISCUSS_FILE, 'decorate-pic');
                if (isset($fileList['code'])) {
                    DB::rollback();
                    return $fileList;
                }
                $discuss->fileList = $fileList;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ResCode::formatError(ResCode::ADD_DISCUSS_FAILED);
        }
        DB::commit();
        $discuss->label_name = $this->getLabelNameById($discuss->label_id);
        return $discuss;
    }

    public function getLabelNameById($labelId) {
        $label = DecorateLabel::select('name')->find($labelId);
        if (!$label instanceof DecorateLabel) {
            return '未知标签';
        }
        return $label->name;
    }

    public function getDiscussDetailById($diaryId)
    {
        $discusses = Discuss::leftjoin('discuss_file as df', 'df.discuss_id', '=', 'discuss.id')
            ->leftjoin('decorate_label as dl', 'discuss.label_id', '=', 'dl.id')
            ->where('discuss.id', $diaryId)
            ->select('discuss.id', 'discuss.content', 'dl.name as label_name', 'discuss.insert_time', 'discuss.modify_time', 'df.file_id', 'df.file_url')
            ->get()->toArray();
        $disucssInfo = [];
        foreach ($discusses as $discuss) {
            if (!isset($disucssInfo['id'])) {
                $disucssInfo = $discuss;
                unset($disucssInfo['file_id']);
                unset($disucssInfo['file_url']);
            }
            if (!empty($discuss['file_id']) && !empty($discuss['file_url'])) {
                $disucssInfo['fileList'][] = ['id' => $discuss['file_id'], 'url' => $discuss['file_url']];
            }
        }
        return $disucssInfo;
    }

    public function delDiscussById($discussId, $uid)
    {
        $ret = Discuss::where('id', $discussId)->where('uid', $uid)->delete();
        if ($ret) {
            DiscussFile::where('discuss_id', $discussId)->delete();
        }
    }

    public function getDiscussList($start = 0, $limit = 15)
    {
        $query = Discuss::leftjoin('discuss_file as df', 'df.discuss_id', '=', 'discuss.id')
            ->select('discuss.id', 'discuss.uid', 'discuss.label_id', 'discuss.content', 'discuss.insert_time', 'df.file_id', 'df.file_url')
            ->orderBy('discuss.id', 'desc');
        if ($start > 0) {
            $query = $query->where('discuss.id', '<', $start);
        }
        $discusses = $query->take(($limit + 1) * 9)->get()->toArray();

        if (empty($discusses)) {
            return ['start' => 0, 'more' => 0, 'data' => (object)[]];
        }

        $uids = [];
        $discussList = [];
        $count = 0;
        $more = 0;
        // $diaryIds = [];
        foreach ($discusses as $discuss) {
            if (!in_array($discuss['uid'], $uids)) {
                $uids[] = $discuss['uid'];
            }
            if ($count > $limit) {
                break;
            }
            // $diaryIds[] = $diary['id'];
            if (!isset($discussList[$discuss['id']])) {
                $temp = $discuss;
                unset($temp['file_id']);
                unset($temp['file_url']);
                $discussList[$discuss['id']] = $temp;
                $discussList[$discuss['id']]['counter'] = DiscussRedis::getInstance()->getCounter($discuss['id']);
                $count ++;
            }
            if (!empty($discuss['file_id']) && !empty($discuss['file_url'])) {
                $discussList[$discuss['id']]['fileList'][] = ['id' => $discuss['file_id'], 'url' => $discuss['file_url']];
            }
        }

        $usersInfo = UserModule::getInstance()->getUserInfoByBatch($uids, ['uid', 'avatar', 'nick_name']);
        foreach ($discussList as &$discuss) {
            if (isset($usersInfo[$discuss['uid']])) {
                $discuss['user'] = $usersInfo[$discuss['uid']];
            }
        }
        if ($count > $limit) {
            $more = 1;
            $discussList = array_slice($discussList, 0, $limit);
        }
        $start = end($discussList)['id'];
        return ['start' => $start, 'more' => $more, 'data' => array_values($discussList)];
    }

    public function commentDiscuss(array $data)
    {
        $discuss = Discuss::select('id')->where('id', $data['discuss_id'])->first();
        if (!$discuss instanceof Discuss) {
            return ResCode::formatError(ResCode::DISCUSS_NOT_EXIT);
        }
        DB::beginTransaction();
        try {
            $discussCommentData = array_intersect_key($data, DiscussComment::$rules);
            $discussComment = DiscussComment::create($discussCommentData);
            if (!empty($data['fileList'])) {
                $fileList = FileModule::getInstance()->add($discussComment->id, $data['fileList'], FileType::DISCUSS_COMMENT_FILE, 'decorate-pic');
                if (isset($fileList['code'])) {
                    DB::rollback();
                    return $fileList;
                }
                $discussComment->fileList = $fileList;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return ResCode::formatError(ResCode::ADD_DISCUSS_FAILED);
        }
        DB::commit();
        DiscussRedis::getInstance()->comment($data['discuss_id']);
        return $discussComment;
    }

    public function formatDiscussData(array $diaries)
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

    public function getDiscussCommentList($discussId, $start, $limit)
    {
        $query = DiscussComment::leftjoin('discuss_comment_file as dcf', 'dcf.discuss_comment_id', '=', 'discuss_comment.id')
            ->select('discuss_comment.id', 'discuss_comment.uid', 'discuss_comment.content', 'discuss_comment.parent_id', 'discuss_comment.target_uid', 'dcf.file_id', 'dcf.file_url',
                'discuss_comment.insert_time') // 'dc.id as r_id', 'dc.uid as r_uid', 'dc.content as r_content', 'dc.parent_id as r_parent_id', 'dc.insert_time as r_insert_time')
            //->leftjoin('discuss_comment as dc', 'dc.parent_id', '=', 'discuss_comment.id')
            ->where('discuss_comment.discuss_id', $discussId)
            ->where('discuss_comment.parent_id', 0)
            ->orderBy('discuss_comment.id', 'desc');
        if ($start > 0) {
            $query = $query->where('discuss_comment.id', '<', $start);
        }
        $discussComments = $query->take(($limit + 1) * 9)->get()->toArray();

        $more = 0;
        if (empty($discussComments)) {
            return ['start' => $start, 'more' => $more, 'data' => (object)[]];
        }
        $discussComments = $this->formatDiscussData($discussComments);
        if (count($discussComments) > $limit) {
            $more = 1;
            $discussComments = array_slice($discussComments, 0, $limit);
        }
        $discussComments = array_values($discussComments);
        $start = end($discussComments)['id'];
        // 获取二级评论
        $commentIds = [];
        $replyComments = [];
        foreach ($discussComments as $discussComment) {
            $commentIds[] = $discussComment['id'];
        }

        if ($commentIds) {
            $replyComments = DiscussComment::leftjoin('discuss_comment_file as dcf', 'dcf.discuss_comment_id', '=', 'discuss_comment.id')
                ->select('discuss_comment.id', 'discuss_comment.uid', 'discuss_comment.content', 'discuss_comment.parent_id', 'discuss_comment.target_uid', 'dcf.file_id', 'dcf.file_url',
                    'discuss_comment.insert_time')
                ->whereIn('discuss_comment.parent_id', $commentIds)
                ->get()->toArray();
            $replyComments = $this->formatDiscussData($replyComments);
        }
        $discussCommentList = array_map(function($item) use ($replyComments) {
            foreach ($replyComments as $replyComment) {
                if ($item['id'] == $replyComment['parent_id']) {
                    $item['reply_comment'][] = $replyComment;
                }
            }
            return $item;
        }, $discussComments);

        return ['start' => $start, 'more' => $more, 'data' => $discussCommentList];
    }

    public function getDiscussByIds(array $discussIds)
    {
        if (!$discussIds) {
            return [];
        }
        $discusses = Discuss::leftjoin('discuss_file as df', 'df.discuss_id', '=', 'discuss.id')
            ->select('discuss.id', 'discuss.title', 'discuss.uid', 'discuss.label_id', 'discuss.content', 'discuss.insert_time', 'df.file_id', 'df.file_url')
            ->orderBy('discuss.id', 'asc')
            ->where('discuss.uid', $uid)
            ->get()->toArray();
        $discussesList = array_values($this->formatDiscussData($discusses));
        $data = array_map(function ($discuss) {
            $discuss['counter'] = DiscussRedis::getInstance()->getCounter($discuss['id']);
            return $discuss;
        }, $discussesList);
        return $data;
    }
}