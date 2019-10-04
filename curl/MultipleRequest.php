<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/10/4
 * Time: 14:16
 */

namespace CurlRequest;

/**
 * Class MultipleRequest
 * @package CurlRequest
 */
class MultipleRequest extends CurlRequest
{
    /**
     * @var resource
     */
    protected $mCurlHandler = null;

    /**
     *  批量请求
     * @param array $params 二维数组
     *                      [
     *                      [
     *                      $path,
     *                      $params,
     *                      $method,
     *                      $headers = [],
     *                      $cookies=[]
     *                      ]
     *                      ]
     * @return array|null
     */
    public function request(array $params)
    {
        $results = [];
        if (is_null($this->mCurlHandler)) {
            //初始化
            $this->mCurlHandler = static::getMultiCurlHandler();
        }
        $multiCurlPool = [];
        foreach ($params as $k => $item) {
            $multiCurlPool[$k] = static::getCurlHandler();
            $this->setOptions($multiCurlPool[$k], $item['method'], $item['path'], $item['params'] ?? '',
                $item['headers'] ?? [], $item['cookies'] ?? '');
            curl_multi_add_handle($this->mCurlHandler, $multiCurlPool[$k]);
        }

        $active = 1;
        do {
            //开始发送请求
            while (($mrc = curl_multi_exec($this->mCurlHandler, $active)) == CURLM_CALL_MULTI_PERFORM)
                ;
            if ($mrc != CURLM_OK) {
                return $results;
            }

            while ($done = curl_multi_info_read($this->mCurlHandler)) {
                $reqKey = array_search($done['handle'], $multiCurlPool);
                $errno  = $done['result'];
                if ($errno == 0) {
                    $result = curl_multi_getcontent($done['handle']);
                } else {
                    $result = null;
                }
                $curlInfo = curl_getinfo($done['handle']);
                $errno    = curl_errno($done['handle']);
                $errmsg   = curl_error($done['handle']);

                curl_multi_remove_handle($this->mCurlHandler, $done['handle']);
                curl_close($done['handle']);
                $results[$reqKey] = compact('result', 'curlInfo', 'errno', 'errmsg');
            }

        } while ($active);

        return $results;
    }

    /**
     * 释放资源
     */
    public function __destruct()
    {
        if (is_resource($this->mCurlHandler)) {
            curl_multi_close($this->mCurlHandler);
        }
    }
}

