<?php
namespace Ycf\Controller;

use Ycf\Core\YcfCore;

class CtrHello
{

    public function actionIndex()
    {
        //echo "hello ycf, it is time: " . time();
        YcfCore::$response->end("Greet, Klcoze! 123333");

    }
    public function actionHello()
    {
        echo "hello ycf" . time();
        echo $this->getPPP();

    }
    public function actionCache()
    {
        YcfCore::$cache->set('kcloze', time() . "_ooo_123", 3600);

        var_dump(YcfCore::$cache->get('kcloze'));
    }

    public function actionTask()
    {
        // send a task to task worker.
        $param = array(
            'action' => 'test',
            'time'   => time(),
        );
        //var_dump(HttpServer::getInstance()->http);
        //$this->http->task(json_encode($param));
        for ($i = 0; $i < 1; $i++) {
            $taskId = YcfCore::$httpServer->task(json_encode($param));
        }
        echo $taskId . " hello ycf" . time();

    }

    public function actionLog()
    {
        //for ($i = 0; $i < 1000; $i++) {
        YcfCore::$log->log('hello ycf' . time(), 'info');
        YcfCore::$log->log('hello 123' . time(), 'info');
        YcfCore::$log->log('hello 123' . time(), 'info');
        YcfCore::$log->log('hello 123' . time(), 'info');
        YcfCore::end("Greet, Klcoze!");
        //}
    }

}
