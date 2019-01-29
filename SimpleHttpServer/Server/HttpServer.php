<?php
/**
 * Desc: Http服务器
 * User: baagee
 * Date: 2018/12/24
 * Time: 下午3:52
 */

namespace SimServer;
/**
 * Class HttpServer
 * @package SimServer
 */
class HttpServer
{
    /**
     * 默认访问的文件
     */
    protected $index = 'index.html';
    /**
     * @var string ip地址
     */
    protected $ip = '127.0.0.1';
    /**
     * @var string web root 根目录
     */
    protected $web_root = '';
    /**
     * @var int 端口号
     */
    protected $port = 8888;

    /**
     * @var null|resource
     */
    protected $socket = null;

    /**
     * @var int
     */
    protected $worker_number = 5;

    /**
     * @var string
     */
    protected $main_app = '';

    /**
     * HttpServer constructor.
     * @param string $webroot       根目录
     * @param string $ip            IP
     * @param int    $port          端口
     * @param string $http_log_dir  log目录
     * @param string $main_app      app入口类
     * @param string $index         默认页面
     * @param int    $worker_number 子进程数量
     */
    public function __construct($webroot, $ip = '127.0.0.1', $port = 8888, $http_log_dir = __DIR__, $main_app = '', $index = 'index.html', $worker_number = 5)
    {
        $this->web_root      = $webroot;
        $this->ip            = $ip;
        $this->port          = $port;
        $this->main_app      = $main_app;
        $this->index         = $index;
        $this->worker_number = $worker_number;
        $this->socket        = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        ServerLog::init($http_log_dir);
        $this->bind();
        $this->listen();
    }

    /**
     * 开始监听
     */
    protected function listen()
    {
        if (!socket_listen($this->socket, 4)) {
            ServerLog::record(sprintf('socket_listen failed %s', socket_strerror(socket_last_error())));
            die;
        }
        ServerLog::record(sprintf('socket listening on %s:%d', $this->ip, $this->port));
        ServerLog::record(sprintf('http://%s:%d', $this->ip, $this->port));
    }

    /**
     * 绑定端口
     */
    protected function bind()
    {
        if (!socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            ServerLog::record('Unable to set option on socket: ' . socket_strerror(socket_last_error()));
            die;
        }
        if (!socket_bind($this->socket, $this->ip, $this->port)) {
            ServerLog::record(sprintf('socket_bind failed on %s:%d %s', $this->ip, $this->port, socket_strerror(socket_last_error())));
            die;
        }
    }

    /**
     * 获取客户端
     * @return resource
     */
    protected function getClient()
    {
        return socket_accept($this->socket);
    }

    /**
     * 获取客户端请求
     * @param $client
     * @return Request
     */
    protected function getRequest($client): Request
    {
        return new Request($client);
    }

    /**
     * 开始运行
     */
    public function run()
    {
        if (!function_exists('pcntl_fork')) {
            // 单进程版
            while ($client = $this->getClient()) {
                if ($client !== false) {
                    $request = $this->getRequest($client);
                    ServerLog::record('Request:' . PHP_EOL . $request->raw_request);
                    $this->handler($request, new Response(), $client);
                    $this->closeClient($client);
                }
            }
        } else {
            // 多进程版
            for ($i = 1; $i <= $this->worker_number; $i++) {
                $pid = pcntl_fork();
                if ($pid == -1) {
                    ServerLog::record('Fork fail');
                } elseif ($pid == 0) {
                    // 子进程
                    $id = getmypid();
                    ServerLog::record("Child process pid=" . $id);
                    while ($client = $this->getClient()) {
                        if ($client !== false) {
                            $request = $this->getRequest($client);
                            ServerLog::record('Child pid=' . $id . ' get request:' . PHP_EOL . $request->raw_request);
                            $this->handler($request, new Response(), $client);
                            ServerLog::record('Child over pid=' . $id);
                            $this->closeClient($client);
                        }
                    }
                    exit(0);
                }
            }
        }
    }

    /**
     * 处理请求
     * @param Request  $request
     * @param Response $response
     * @param          $client
     * @throws \Exception
     */
    protected function handler(Request $request, Response $response, $client)
    {
        $file = $this->web_root . $request->path;
        if (is_file($file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (array_key_exists($ext, MIMETypes::MIME_TYPE_MAP)) {
                $mime_type = MIMETypes::MIME_TYPE_MAP[$ext];
            } else {
                $mime_type = MIMETypes::TEXT_PLAIN;
            }
            Response::setHeader('Content-Type', $mime_type);
            $response->setBody(file_get_contents($file));
        } else {
            if ($request->path == '/') {
                // 默认index.html
                $index_file = $this->web_root . DIRECTORY_SEPARATOR . $this->index;
                if (is_file($index_file)) {
                    Response::setHeader('Content-Type', MIMETypes::TEXT_HTML);
                    $response->setBody(file_get_contents($index_file));
                } else {
                    $response->setStatusCode(404);
                }
            } else {
                if (!empty($request->path) && $request->path !== '/favicon.ico' && $this->main_app !== '') {
                    // /a/b/c  /aa/bb/cc /aa/bb
                    try {
                        $app_class = $this->main_app;
                        $app       = new $app_class($request);
                        if ($app instanceof AppBase) {
                            $res = $app->run();
                            $response->setBody($res);
                        } else {
                            throw new \Exception(sprintf("%s not instanceof AppBase", $app_class));
                        }
                    } catch (\Throwable $e) {
                        Response::setStatusCode(500);
                        ServerLog::record('Server 500 Error:' . $e->getMessage());
                    }
                } else {
                    Response::setStatusCode(404);
                }
            }
        }
        $response->send($client);
    }

    /**
     * 关闭客户端链接
     * @param $client
     */
    public function closeClient($client)
    {
        socket_close($client);
        ServerLog::record('Client closed' . PHP_EOL . str_repeat('-', 60));
    }

    /**
     * 关闭链接 socket
     */
    public function __destruct()
    {
        socket_close($this->socket);
    }
}
