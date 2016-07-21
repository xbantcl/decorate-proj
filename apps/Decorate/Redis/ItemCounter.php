<?php namespace Decorate\Redis;

/**
 * ItemCounter
 */

trait ItemCounter
{
    // 阅读数.
    public function read($dataId)
    {
        return $this->HINCRBY($this->getKey($dataId), 'rc', self::INCR_STEP);
    }

    // 评论数.
    public function comment($dataId)
    {
        return $this->HINCRBY($this->getKey($dataId), 'cc', self::INCR_STEP);
    }

    // 收藏数.
    public function collection($dataId, $uid)
    {
        $this->HINCRBY($this->getKey($dataId), 'sc', self::INCR_STEP);
        return $this->SADD($this->getColKey($dataId), $uid);
    }

    // 收藏数减少.
    public function uncollection($dataId, $uid)
    {
        $this->HINCRBY($this->getKey($dataId), 'sc', self::REDU_STEP);
        return $this->SREM($this->getColKey($dataId), $uid);
    }

    public function collectionUsers($dataId, $uid)
    {
        return $this->SADD($this->getColKey($dataId), $uid);
    }

    /**
     * 判断是否收藏.
     * 
     * @param integer $dataId
     * @param integer $uid
     */
    public function isCollection($dataId, $uid)
    {
        $ret = $this->SISMEMBER($this->getColKey($dataId), $uid);
        if ($ret) {
            return 1;
        }
        return 0;
    }

    public function getCounter($dataId)
    {
        $counter = $this->HGETALL($this->getKey($dataId));
        $counter['rc'] = isset($counter['rc']) ? intval($counter['rc']) : 0;
        $counter['cc'] = isset($counter['cc']) ? intval($counter['cc']) : 0;
        $counter['sc'] = isset($counter['sc']) ? intval($counter['sc']) : 0;
        return $counter;
    }

}