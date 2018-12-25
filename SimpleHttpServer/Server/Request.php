<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/24
 * Time: 下午4:04
 */

namespace SimServer;

/**
 * Class RequestBody
 * @package SimServer
 */
class Request
{
    /**
     * @var string 原始请求体
     */
    public $raw_request = '';
    public $cookie      = [];
    /**
     * @var string 请求方法
     */
    public $method = '';
    /**
     * @var array get请求参数
     */
    public $get_params = [];
    /**
     * @var array post请求参数
     */
    public $post_params = [];
    /**
     * @var string 请求路径
     */
    public $path = '';
    /**
     * @var string 请求get参数字符串
     */
    public $query_string = '';
    /**
     * @var string path+query_string
     */
    public $uri = '';

    /**
     * RequestBody constructor.
     * @param $client
     */
    public function __construct($client)
    {
        $this->raw_request = $this->getRawRequest($client);
        $this->parseRawRequest();
    }

    /**
     * 获取客户端原始请求
     * @param $client
     * @return string
     */
    protected function getRawRequest($client)
    {
        return socket_read($client, 4096);
    }

    /**
     * 解析原始请求体
     */
    protected function parseRawRequest()
    {
        if (!empty($this->raw_request)) {
            $tmp = explode(PHP_EOL, $this->raw_request);
            list($this->method, $this->uri, $this->protocol) = array_map(function ($i) {
                $i = trim($i);
                return $i;
            }, explode(' ', $tmp[0]));

            unset($tmp[0]);
            $flag = false;
            foreach ($tmp as $item) {
                $item = trim($item);
                if (!empty($item)) {
                    if ($flag) {
                        parse_str($item, $query_array);
                        $this->post_params = $query_array;
                    } else {
                        $item = explode(': ', $item);
                        if (strtolower($item[0]) == 'cookie') {
                            $cookies = [];
                            foreach (explode('; ', $item[1]) as $cookie) {
                                $cookie              = explode('=', $cookie);
                                $cookies[$cookie[0]] = $cookie[1];
                            }
                            $this->cookie = $cookies;
                        } else {
                            $this->{strtolower(str_replace('-', '_', $item[0]))} = $item[1];
                        }
                    }
                } else {
                    if ($this->method == 'POST') {
                        $flag = true;
                    }
                }
            }
            $i                  = explode('?', $this->uri);
            $this->path         = $i[0] ?? '';
            $this->query_string = $i[1] ?? '';
            parse_str($this->query_string, $query_array);
            $this->get_params = $query_array;
        }
    }
}