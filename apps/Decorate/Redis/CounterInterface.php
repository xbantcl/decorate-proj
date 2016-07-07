<?php namespace Decorate\Redis;

/**
 * CounterInterface
 */

interface CounterInterface
{
    const INCR_STEP = 1;
    const REDU_STEP = -1;

    function getKey($dataId);
}