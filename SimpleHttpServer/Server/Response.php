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
        if (!array_key_exists($code, StatusCode::STATUS_MAP)) {
            throw new \Exception('Code ' . $code . ' not allowed');
        } else {
            self::$code = $code;
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
        $response                = sprintf('HTTP/1.1 %d %s' . PHP_EOL, self::$code, StatusCode::STATUS_MAP[self::$code]);
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
        ServerLog::record(sprintf('Response:' . PHP_EOL . '%s', $response));
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