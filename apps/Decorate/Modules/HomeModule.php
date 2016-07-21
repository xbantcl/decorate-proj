<?php namespace Decorate\Modules;
/**
 * HomeModule class.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Utils\Help;
use Decorate\Enum\ResCode;
use Decorate\Models\Recommend;
use Decorate\Redis\RecommendRedis;

class HomeModule extends BaseModule
{
    /**
     * 获取推荐作品列表.
     * 
     * @param integer $start
     * @param integer $limit
     */
    public function getRecommendList($start, $limit)
    {
        $query = Recommend::orderBy('id', 'DESC');
        if ($start > 0) {
            $query = $query->where('id', '<', $start);
        }
        $recommendList = $query->take($limit + 1)->get()->toArray();
        $more = 0;
        if (empty($recommendList)) {
            return ['start' => 0, 'more' => $more, 'data' => (object)[]];
        }
        if (count($recommendList) > $limit) {
            $more = 1;
            array_pop($recommendList);
        }
        $recommendList = array_map(function($recommend) {
            if (!empty($recommend['cover_url'])) {
                $recommend['fileList'] = explode(',', $recommend['cover_url']);
                unset($recommend['cover_url']);
            }
            $recommend['counter'] = RecommendRedis::getInstance()->getCounter($recommend['id']);
            return $recommend;
        }, $recommendList);
        $start = end($recommendList)['id'];
        return ['start' => $start, 'more' => $more, 'data' => $recommendList];
    }

    public function read($dataId)
    {
        return RecommendRedis::getInstance()->read($dataId);
    }
}
