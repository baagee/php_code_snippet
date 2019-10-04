<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/10/4
 * Time: 14:16
 */

namespace CurlRequest;

/**
 * Class SingleRequest
 * @package CurlRequest
 */
class SingleRequest extends CurlRequest
{
    /**
     * @var null
     */
    protected $curlHandler = null;

    /**
     * @param string $path
     * @param        $params
     * @param string $method
     * @param array  $headers
     * @param string $cookies
     * @return array
     */
    public function request(string $path, $params, string $method, array $headers = [], string $cookies = '')
    {
        if ($this->curlHandler == null) {
            $this->curlHandler = static::getCurlHandler();
        } else {
            curl_reset($this->curlHandler);
        }

        $this->setOptions($this->curlHandler, $method, $path, $params, $headers, $cookies);

        $result = null;
        for ($tryTimes = 0; $tryTimes <= intval($this->config['retry_times']); $tryTimes++) {
            $result   = curl_exec($this->curlHandler);
            $curlInfo = curl_getinfo($this->curlHandler);
            $errno    = curl_errno($this->curlHandler);// 错误码
            $errmsg   = curl_error($this->curlHandler); // 错误信息
            if ($errno == 0) {
                break;
            } else {
                if ($tryTimes == intval($this->config['retry_times'])) {
                    // 出错
                    throw new \RuntimeException($errmsg, $errno);
                } else {
                    $tryTimes++;
                }
            }
        }

        return compact('result', 'curlInfo', 'errno', 'errmsg');
    }

    /**
     * 释放资源
     */
    public function __destruct()
    {
        if (is_resource($this->curlHandler)) {
            curl_close($this->curlHandler);
        }
    }
}


