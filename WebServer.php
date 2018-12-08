<?php

/**
 * Desc:
 * User: baagee
 * Date: 2018/12/7
 * Time: 下午12:01
 */
class WebServer
{
    public $app;
    public $server;

    public function __construct($host, $port)
    {
        $this->server = new \swoole_http_server($host, $port);
    }

    public function start()
    {
        // $this->server->on('start', array($this, 'onStart'));
        // $this->server->on('shutdown', array($this, 'onShutdown'));
        // $this->server->on('workerStop', array($this, 'onWorkerStop'));
        $this->server->on('workerStart', array($this, 'onWorkerStart'));
        $this->server->on('request', array($this, 'onRequest'));
        $this->server->start();
    }

    public function onWorkerStart($serv, $worker_id)
    {
        // 应用初始化
        $this->app = 1;
    }

    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {
        $app = $this->app;
        // 处理用户请求
        $get = json_encode($request->get);
        // 响应用户请求
        $response->end("App is {$app}. Get {$get}");
    }
}

$s = new WebServer("127.0.0.1", 9080);
$s->start();