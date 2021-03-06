<?php
namespace Ycf\Core;

use Ycf\Core\YcfCore;
use Ycf\Core\YcfHttpServer;
use Ycf\Model\ModelTask;

class YcfHttpServer
{
    public static $instance = null;

    public $http = null;
    public static $get;
    public static $post;
    public static $header;
    public static $server;

    public $response = null;

    public function __construct()
    {
        date_default_timezone_set('Asia/Shanghai');
        define('DEBUG', true);
        define('SWOOLE', true);
        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT_PATH', realpath(dirname(__FILE__)) . DS . ".." . DS . ".." . DS);

        $this->http = new \swoole_http_server("0.0.0.0", 9501);

        $this->http->set(
            array(
                'worker_num'      => 2,
                'daemonize'       => true,
                'max_request'     => 1,
                'task_worker_num' => 1,
                'log_file'        => ROOT_PATH . 'src/runtime/swoole.log',
                //'dispatch_mode' => 1,
            )
        );

        $this->http->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->http->on('WorkerStop', array($this, 'onWorkerStop'));
        $this->http->on('Start', array($this, 'onStart'));

        $this->http->on('request', function ($request, $response) {
            define('YCF_BEGIN_TIME', microtime(true));
            //捕获异常
            register_shutdown_function(array($this, 'handleFatal'));
            //请求过滤
            if ('/favicon.ico' == $request->server['path_info'] || '/favicon.ico' == $request->server['request_uri']) {
                return $response->end();
            }
            if (isset($request->server)) {
                self::$server = $request->server;
                foreach ($request->server as $key => $value) {
                    $_SERVER[strtoupper($key)] = $value;
                }
            }
            if (isset($request->header)) {
                self::$header = $request->header;
            }
            if (isset($request->get)) {
                self::$get = $request->get;
                foreach ($request->get as $key => $value) {
                    $_GET[$key] = $value;
                }
            }
            if (isset($request->post)) {
                self::$post = $request->post;
                foreach ($request->post as $key => $value) {
                    $_POST[$key] = $value;
                }
            }
            if (isset($request->request_uri)) {
                $_SERVER['REQUEST_URI'] = $request->request_uri;
            }
            //$GLOBALS['httpServer'] = $this->http;
            ob_start();
            //实例化ycf对象
            try {
                $ycf                 = new YcfCore;
                YcfCore::$response   = $response;
                YcfCore::$httpServer = $this->http;
                $ycf->init();
                $ycf->run();
            } catch (Exception $e) {
                var_dump($e);
            }
            $result = ob_get_contents();
            ob_end_clean();
            YcfCore::end($result);
            unset($result);
        });

        $this->http->on('Task', array($this, 'onTask'));
        $this->http->on('Finish', array($this, 'onFinish'));

        $this->http->start();
    }
    public function onStart()
    {
        //echo "start_master_pid: " . $this->http->master_pid . "\n";
        //echo "start_manager_pid: " . $this->http->manager_pid . "\n";
        file_put_contents(ROOT_PATH . 'src/runtime/master.pid', $this->http->master_pid);

    }
    public function onWorkerStart()
    {

        require ROOT_PATH . 'vendor/autoload.php';
        YcfCore::$settings = parse_ini_file(ROOT_PATH . "src/config/settings.ini.php", true);

    }
    public function onWorkerStop()
    {
        opcache_reset(); //清空zend_opcache的缓存
    }
    public function onTask($serv, $task_id, $from_id, $data)
    {
        $ycf = new YcfCore;
        $ycf->init();
        return ModelTask::run($serv, $task_id, $from_id, $data);
    }
    public function onFinish($serv, $task_id, $data)
    {
        echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
        unset($data);
    }
    /**
     * Fatal Error的捕获
     *
     */
    public function handleFatal()
    {
        $error = error_get_last();
        if (!isset($error['type'])) {
            return;
        }

        switch ($error['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_DEPRECATED:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
                break;
            default:
                return;
        }
        $message = $error['message'];
        $file    = $error['file'];
        $line    = $error['line'];
        $log     = "\n异常提示：$message ($file:$line)\nStack trace:\n";
        $trace   = debug_backtrace(1);

        foreach ($trace as $i => $t) {
            if (!isset($t['file'])) {
                $t['file'] = 'unknown';
            }
            if (!isset($t['line'])) {
                $t['line'] = 0;
            }
            if (!isset($t['function'])) {
                $t['function'] = 'unknown';
            }
            $log .= "#$i {$t['file']}({$t['line']}): ";
            if (isset($t['object']) && is_object($t['object'])) {
                $log .= get_class($t['object']) . '->';
            }
            $log .= "{$t['function']}()\n";
        }
        if (isset($_SERVER['REQUEST_URI'])) {
            $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];
        }
        YcfCore::$log->log($log, 'fatal');
        YcfCore::$log->sendTask();
        if (YcfCore::$response) {
            YcfCore::$response->status(500);
            YcfCore::$response->end('程序异常');
        }

        unset($this->response);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new YcfHttpServer();
        }
        return self::$instance;
    }
}

YcfHttpServer::getInstance();
