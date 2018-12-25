<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/24
 * Time: 下午4:04
 */

namespace SimServer;
/**
 * Class ResponseBody
 * @package SimServer
 */
class Response
{
    /**
     * @var array 保存headers
     */
    protected static $headers = [];
    /**
     * @var string 响应内容
     */
    protected $body = '';

    /**
     * @var array 状态码
     */
    protected static $status_code = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Moved Temporarily ',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    /**
     * @var int 响应状态码
     */
    protected static $code = 200;

    /**
     * @var array 保存响应Cookie
     */
    protected static $cookie = [];

    /**
     * 设置Cookie
     * @param $key
     * @param $val
     */
    public static function setHeader($key, $val)
    {
        self::$headers[$key] = $val;
    }

    /**
     * 设置状态码
     * @param $code
     * @throws \Exception
     */
    public static function setStatusCode($code)
    {
        if (in_array($code, array_keys(self::$status_code))) {
            self::$code = $code;
        } else {
            throw new \Exception('Code ' . $code . ' not allowed');
        }
    }

    /**
     * 设置Cookie
     * @param $k
     * @param $v
     */
    public static function setCookie($k, $v)
    {
        self::$cookie[$k] = $v;
    }

    /**
     * 发送
     * @param $client
     */
    public function send($client)
    {
        $response                = sprintf('HTTP/1.1 %d %s' . PHP_EOL, self::$code, self::$status_code[self::$code]);
        self::$headers['status'] = self::$code;
        self::$headers['date']   = date('Y-m-d H:i:s');
        if (!empty(self::$cookie)) {
            $cookie_str = '';
            foreach (self::$cookie as $k => $v) {
                $cookie_str .= $k . '=' . $v . '; ';
            }
            $response .= 'set-cookie: ' . $cookie_str . PHP_EOL;
        }
        foreach (self::$headers as $key => $val) {
            $response .= $key . ': ' . $val . PHP_EOL;
        }
        $response .= PHP_EOL . $this->body;
        socket_write($client, $response, strlen($response));
        self::reset();
    }

    /**
     * 结束一次请求后重置默认值
     */
    protected static function reset()
    {
        self::$headers = [];
        self::$code    = 200;
    }

    /**
     * 设置响应体
     * @param $content
     */
    public function setBody($content)
    {
        $this->body = $content;
    }
}