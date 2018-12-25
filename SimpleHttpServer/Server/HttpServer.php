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
    const INDEX = 'index.html';
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
     * Server constructor.
     * @param string $webroot
     * @param string $ip
     * @param int    $port
     * @param string $http_log_dir
     */
    public function __construct($webroot, $ip = '127.0.0.1', $port = 8888, $http_log_dir = __DIR__)
    {
        $this->web_root = $webroot;
        $this->ip       = $ip;
        $this->port     = $port;
        $this->socket   = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        ServerLog::init($http_log_dir);
        $this->bind();
        $this->listen();
    }

    /**
     * 开始监听
     */
    protected function listen()
    {
        if (socket_listen($this->socket, 4) === false) {
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
        if (socket_bind($this->socket, $this->ip, $this->port) === false) {
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
        $client = socket_accept($this->socket);
        return $client;
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
        // 主进程
        $pid = getmypid();
        ServerLog::record(sprintf("Master process runing pid=%d", $pid));
        while ($client = $this->getClient()) {
            if ($client !== false) {
                if (!function_exists('pcntl_fork')) {
                    // 单进程版
                    $request = $this->getRequest($client);
                    ServerLog::record('Request:' . PHP_EOL . $request->raw_request);
                    $this->handler($request, new Response(), $client);
                    $this->closeClient($client);
                } else {
                    // 多进程版
                    $pid = pcntl_fork();
                    if ($pid == -1) {
                        ServerLog::record('fork fail');
                    } elseif ($pid) {
                        while (true) {
                            $res = pcntl_waitpid($pid, $status, WNOHANG);
                            if ($res == -1 || $res > 0) {
                                $this->closeClient($client);
                                break;
                            }
                        }
                    } else {
                        // 子进程
                        $id = getmypid();
                        ServerLog::record("Child process pid=" . $id);
                        $request = $this->getRequest($client);
                        ServerLog::record('Request:' . PHP_EOL . $request->raw_request);
                        $this->handler($request, new Response(), $client);
                        ServerLog::record('child over pid=' . $id);
                        exit();
                    }
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
                $index_file = $this->web_root . DIRECTORY_SEPARATOR . self::INDEX;
                if (is_file($index_file)) {
                    Response::setHeader('Content-Type', MIMETypes::TEXT_HTML);
                    $response->setBody(file_get_contents($index_file));
                } else {
                    $response->setStatusCode(404);
                }
            } else {
                if (!empty($request->path) && $request->path !== '/favicon.ico') {
                    // /a/b/c  index.php/aa/bb/cc index.html/aa/bb
                    try {
                        $app = new \App\App($request);
                        $res = $app->run();
                        $response->setBody($res);
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
     *
     */
    public function __destruct()
    {
        socket_close($this->socket);
    }
}
