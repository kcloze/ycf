<?php
namespace Ycf\Core;

use Ycf\Core\YcfCore;
use Ycf\Core\YcfRedis;

class YcfCache
{
    public $cache;
    public function __construct()
    {
        $this->cache = new YcfRedis(YcfCore::$settings['Redis']);
    }

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function set($key, $value, $timeout = 0)
    {
        $this->cache->set($key, $value, $timeout);
    }
}
