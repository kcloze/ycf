<?php
namespace Ycf\Model;

use Ycf\Core\YcfCore;

class ModelRedis extends ModelBase
{
    private $redis = null;
    public function __construct()
    {
        $this->redis = parent->load('redis');
    }

    public function testRedis()
    {
        $this->redis->sadd('test1', 1);
        $this->redis->sadd('test1', 2);
        $this->redis->sadd('test1', 3);
        $this->redis->sadd('test2', 2);
        $this->redis->sdiffstore('test3', array('test1', 'test2'));
        var_dump($this->redis->smembers('test3'));
        // Use Redis commands
        $this->redis->set('test', '7');
        var_dump($this->redis->get('test'));
    }

}
