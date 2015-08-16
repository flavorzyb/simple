<?php
/**
 * Created by PhpStorm.
 * User: flavor
 * Date: 15/8/16
 * Time: 下午5:21
 */

namespace Simple\Redis;


class RedisServer
{
    protected $serverArray  = [];

    public function __construct(array $serverArray)
    {
    }

    public function getClient($key)
    {
    }
}
