<?php

namespace Ycf\Core;

use Ycf\Core\YcfCache;
use Ycf\Core\YcfLog;

class YcfCore
{

    static $settings   = array();
    static $response   = null;
    static $log        = null;
    static $cache      = null;
    static $httpServer = null;

    public function init($httpServer = null)
    {
        self::$log = new YcfLog();
        if (isset(self::$settings["Redis"])) {
            self::$cache = new YcfCache();
        }

    }
    //封装输出，避免多次输出
    public static function end($msg)
    {
        if (self::$response) {
            self::$response->end($msg);
            self::$response = null;
        }
    }
    public function run()
    {

        if (php_sapi_name() == "cli" && !defined('SWOOLE')) {
            $router = $this->routeCli();
        } else {
            $router = $this->route();
        }
        //route to controller
        $actionName = 'action' . ucfirst($router['action']);
        $ycfName    = "Ycf\Controller\Ctr" . ucfirst($router['controller']);
        if (method_exists($ycfName, $actionName)) {
            try {
                $ycf = new $ycfName();
                $ycf->$actionName();
            } catch (Exception $e) {
                var_dump($e);
            }

        } else {
            echo ("action not find");
        }
        $this->shutdown();

    }

    public function shutdown()
    {
        //echo 'shutdown....';
        self::$log->flush();
        // if (!defined('SWOOLE')) {
        //     self::$log->flush();
        // } else {
        //     self::$log->sendTask();
        // }
    }

    public function route()
    {
        $array = array('controller' => 'Hello', 'action' => 'index');
        if (!empty($_GET["ycf"])) {
            $array['controller'] = $_GET["ycf"];
        }
        if (!empty($_GET["act"])) {
            $array['action'] = $_GET["act"];
            return $array;
        }
        $uri = parse_url($_SERVER['REQUEST_URI']);
        if (empty($uri['path']) or '/' == $uri['path'] or '/index.php' == $uri['path']) {
            return $array;
        }
        $request = explode('/', trim($uri['path'], '/'), 3);
        if (count($request) < 2) {
            return $array;
        }
        $array['controller'] = $request[0];
        $array['action']     = $request[1];

        return $array;
    }
    /**
     *cli use this:  /opt/php7/bin/php index.php ycf=Pdo act=test
     *
     */
    public function routeCli()
    {
        $array = array('controller' => 'Hello', 'action' => 'index');
        global $argv;
        foreach ($argv as $arg) {
            $e = explode("=", $arg);
            if (count($e) == 2) {
                $_GET[$e[0]] = $e[1];
            } else {
                $_GET[$e[0]] = 0;
            }
        }
        if (!empty($_GET["ycf"])) {
            $array['controller'] = $_GET["ycf"];
        }
        if (!empty($_GET["act"])) {
            $array['action'] = $_GET["act"];
        }
        //$_SERVER['REQUEST_URI'] = $argv[0] . "?" . $argv[1] . "&" . $argv[2];
        return $array;
    }

}
