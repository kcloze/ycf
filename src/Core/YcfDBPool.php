<?php
#namespace Ycf\Core;
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', realpath(dirname(__FILE__)) . DS . ".." . DS . ".." . DS);
/**
 * 数据库连接池
 */
class YcfDBPool
{

    public function run($argv)
    {
        //var_dump($argv);exit;
        ini_set('memory_limit', '2048M');
        ini_set("display_errors", "On");
        $conf = ROOT_PATH . "src/config/pool.ini"; //pool_server的配置文件

        if (empty($argv[1])) {
            echo "Usage: pool_server {start|stop|restart}\n";
            exit;
        }

        $cmd = $argv[1];
        if (($conf_arr = parse_ini_file($conf, true)) === false) //for stop && reload && test ini
        {
            die("bad ini file\n");
        }

        switch ($cmd) {
            case "start":
                pool_server_create($conf);
                break;
            case "reload":
                pool_server_reload((int) file_get_contents('/var/run/con_pool_.pid'));
                echo "Tips: The reload can only modify 'pool_min','pool_max','recycle_num' and 'idel_time'\n";
                die;
                break;
            case "stop":
                pool_server_shutdown((int) file_get_contents('/var/run/con_pool_.pid'));
                break;
            case "restart":
                pool_server_shutdown((int) file_get_contents('/var/run/con_pool_.pid'));
                sleep(1);
                pool_server_create($conf);
                break;
            default:
                break;
        }

    }

}

$server = new YcfDBPool($argv);
$server->run($argv);
