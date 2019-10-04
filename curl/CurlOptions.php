<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/10/4
 * Time: 14:11
 */

namespace CurlRequest;

/**
 * Class CurlOptions
 * @package CurlRequest
 */
class CurlOptions
{
    /**
     * @var null
     */
    protected $curlHandler = null;

    /**
     * CurlOptions constructor.
     * @param $curlHandler
     */
    public function __construct($curlHandler)
    {
        $this->curlHandler = $curlHandler;
    }

    /**
     * @param array  $config
     * @param string $url
     * @param        $params
     * @param string $method
     * @param array  $headers
     * @param string $cookies
     * @return array
     */
    private function buildOptions(array $config, string $url, $params, string $method, array $headers, string $cookies)
    {
        //基本配置
        $options = [
            CURLOPT_CONNECTTIMEOUT_MS => $config['connect_timeout_ms'],
            CURLOPT_TIMEOUT_MS        => $config['timeout_ms'],
            CURLOPT_HEADER            => $config['return_header'],
            CURLOPT_RETURNTRANSFER    => 1,
            CURLOPT_ENCODING          => 'gzip,deflate'
        ];

        // 最大跳转
        if ($config['max_redirs'] > 0) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
            $options[CURLOPT_MAXREDIRS]      = $config['max_redirs'];
        } else {
            $options[CURLOPT_FOLLOWLOCATION] = false;
        }

        // 代理
        if (!empty($config['proxy']['ip']) && !empty($config['proxy']['port'])) {
            // 验证IP
            if (!filter_var($config['proxy']['ip'], FILTER_VALIDATE_IP)) {
                throw new \RuntimeException("proxy.ip不合法");
            }
            $options[CURLOPT_PROXY]     = $config['proxy']['ip'];
            $options[CURLOPT_PROXYPORT] = intval($config['proxy']['port']);
        }

        // referer
        if (!empty($config['referer'])) {
            $options[CURLOPT_REFERER] = $config['referer'];
        }

        // user-agent
        if (!empty($config['user_agent'])) {
            $options[CURLOPT_USERAGENT] = $config['user_agent'];
        }
        // https不验证
        if (stripos($url, 'https://') !== false) {
            // https请求 不验证证书和host
            $options[CURLOPT_SSL_VERIFYPEER] = 0;
            $options[CURLOPT_SSL_VERIFYHOST] = 0;
            $options[CURLOPT_SSLVERSION]     = 1;
        }

        // 请求参数和方法
        $method = strtoupper($method);
        if ($method === 'GET') {
            // GET方式穿参数
            $params = http_build_query($params);
            strpos($url, '?') === false ? $url .= '?' : $url .= '&';
            $url                      .= $params;
            $options[CURLOPT_HTTPGET] = 1;
            $options[CURLOPT_POST]    = 0;
        } else {
            // 非GET方式
            $options[CURLOPT_HTTPGET] = 0;
            if ($method !== 'POST') {
                // 不是POST 比如PUT delete options
                if (is_array($params)) {
                    $params    = json_encode($params, JSON_UNESCAPED_UNICODE);
                    $headers[] = 'Content-Type: application/json';
                } else {
                    $params = strval($params);
                }
            } else {
                // POST传输数据
                if (is_array($params)) {
                    $hasFile = false;
                    foreach ($params as $field => $val) {
                        if ($val instanceof \CURLFile) {
                            $hasFile = true;
                            break;
                        }
                    }
                    if ($hasFile) {
                        //有文件上传 不做处理
                        $headers[] = 'Content-Type: multipart/form-data';
                    } else {
                        // 没有文件 使用urlencoded
                        $params    = http_build_query($params);
                        $headers[] = "Content-Type: application/x-www-form-urlencoded";
                        $headers[] = "Content-Length:" . strlen($params);
                    }
                } elseif (is_string($params)) {
                    $headers[] = "Content-Type: application/x-www-form-urlencoded";
                    $headers[] = "Content-Length:" . strlen($params);
                }
            }
            $options[CURLOPT_CUSTOMREQUEST] = $method;
            $options[CURLOPT_POSTFIELDS]    = $params;
        }

        $headers[] = 'Accept-Encoding: gzip, deflate';
        if (!empty($headers)) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        $options[CURLOPT_URL] = $url;

        // 设置Cookie
        if (!empty($cookies)) {
            $options[CURLOPT_COOKIE] = $cookies;
        }
        return $options;
    }

    /**
     * @param array  $config
     * @param string $path
     * @param        $params
     * @param array  $headers
     * @param string $cookies
     * @param string $method
     * @return $this
     */
    public function setOptions(array $config, string $path, $params, array $headers, string $cookies, string $method)
    {
        $options = $this->buildOptions($config, $path, $params, $method, $headers, $cookies);
        curl_setopt_array($this->curlHandler, $options);
        return $this;
    }

    /**
     * @return resource
     */
    public function getCurlHandler()
    {
        return $this->curlHandler;
    }
}