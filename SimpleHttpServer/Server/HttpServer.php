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
     * @var string Log文件
     */
    protected $http_log_file = __DIR__ . '/http.log';

    /**
     * @var null|resource
     */
    protected $socket = null;

    /**
     * Server constructor.
     * @param string $webroot
     * @param string $ip
     * @param int    $port
     * @param string $http_log_file
     */
    public function __construct($webroot, $ip = '127.0.0.1', $port = 8888, $http_log_file = __DIR__ . '/http.log')
    {
        $this->web_root = $webroot;
        $this->ip       = $ip;
        $this->port     = $port;
        $this->socket   = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $http_log_dir   = realpath(dirname($http_log_file));
        if (!is_dir($http_log_dir)) {
            mkdir($http_log_dir);
        }
        $this->http_log_file = realpath($http_log_file);
        $this->bind();
        $this->listen();
    }

    /**
     * 开始监听
     */
    protected function listen()
    {
        if (socket_listen($this->socket, 4) === false) {
            $this->log(sprintf('socket_listen failed %s', socket_strerror(socket_last_error())));
            die;
        }
        $this->log(sprintf('socket listening on %s:%d', $this->ip, $this->port));
        $this->log(sprintf('http://%s:%d', $this->ip, $this->port));
    }

    /**
     * 绑定端口
     */
    protected function bind()
    {
        if (socket_bind($this->socket, $this->ip, $this->port) === false) {
            $this->log(sprintf('socket_bind failed on %s:%d %s', $this->ip, $this->port, socket_strerror(socket_last_error())));
            die;
        }
    }

    /**
     * 记录Log信息
     * @param $msg
     */
    protected function log($msg)
    {
        $str = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
        echo $str;
        file_put_contents($this->http_log_file, $str, FILE_APPEND | LOCK_EX);
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
        while ($client = $this->getClient()) {
            if ($client !== false) {
                $request = $this->getRequest($client);
                $this->log('Request:' . PHP_EOL . $request->raw_request);
                $this->handler($request, new Response(), $client);
                $this->closeClient($client);
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
                        $this->log('Server 500 Error:' . $e->getMessage());
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
        $this->log('Client closed' . PHP_EOL . str_repeat('-', 60));
    }

    /**
     *
     */
    public function __destruct()
    {
        socket_close($this->socket);
    }
}
