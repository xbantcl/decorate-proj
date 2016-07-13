<?php namespace Decorate\Redis;

/**
 * ItemCounter
 */

trait ItemCounter
{
    // 阅读数.
    public function read($dataId)
    {
        $this->HINCRBY($this->getKey($dataId), 'rc', self::INCR_STEP);
    }

    // 评论数.
    public function comment($dataId)
    {
        $this->HINCRBY($this->getKey($dataId), 'cc', self::INCR_STEP);
    }

    // 收藏数.
    public function collection($dataId, $uid)
    {
        $this->HINCRBY($this->getKey($dataId), 'sc', self::INCR_STEP);
        $this->SADD($this->getColKey($dataId), $uid);
    }

    // 收藏数减少.
    public function uncollection($dataId, $uid)
    {
        $this->HINCRBY($this->getKey($dataId), 'sc', self::REDU_STEP);
        $this->SREM($this->getColKey($dataId), $uid);
    }

    public function collectionUsers($dataId, $uid)
    {
        $this->SADD($this->getColKey($dataId), $uid);
    }

    /**
     * 判断是否收藏.
     * 
     * @param integer $dataId
     * @param integer $uid
     */
    public function isCollection($dataId, $uid)
    {
        return boolval($this->SISMEMBER($this->getColKey($dataId), $uid));
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