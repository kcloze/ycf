<?php
namespace Ycf\Model;

use Ycf\Core\YcfCore;
use Ycf\Core\YcfDB;
use Ycf\Core\YcfRedis;

class ModelBase
{
    protected $db    = null;
    protected $redis = null;
    public function __construct()
    {
        $this->db = $this->load('db');
    }

    protected function load($obj)
    {
        switch ($obj) {
            case 'db':
                return $this->getDbInstance();
                break;
            case 'redis':
                return $this->getRedisInstance();
                break;
            default:
                break;
        }
    }

    protected function getDbInstance()
    {
        // Create Mysql Client instance with you configuration settings
        if (null == $this->db) {
            $this->db = new YcfDB(YcfCore::$settings['Mysql']);
        }
        return $this->db;
    }
    protected function getRedisInstance()
    {
        if (!extension_loaded('redis')) {
            throw new \RuntimeException('php redis extension not found');
            return null;
        }
        // Create Redis Client instance with you configuration settings
        $this->redis = new YcfRedis(YcfCore::$settings['Redis']);
        return $this->redis;
    }
}
