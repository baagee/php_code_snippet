<?php
/**
 * Curl模拟Http工具类
 * Class HttpCurl
 */

/*
HTTP请求的方法：
HTTP/1.1协议中共定义了八种方法（有时也叫“动作”），来表明Request-URL指定的资源不同的操作方式

    1、OPTIONS
        返回服务器针对特定资源所支持的HTTP请求方法，也可以利用向web服务器发送‘*’的请求来测试服务器的功能性
    2、HEAD
        向服务器索与GET请求相一致的响应，只不过响应体将不会被返回。这一方法可以再不必传输整个响应内容的情况下，就可以获取包含在响应小消息头中的元信息。
    3、GET
        向特定的资源发出请求。它本质就是发送一个请求来取得服务器上的某一资源。资源通过一组HTTP头和呈现数据（如HTML文本，或者图片或者视频等）返回给客户端。GET请求中，永远不会包含呈现数据。
    4、POST
        向指定资源提交数据进行处理请求（例如提交表单或者上传文件）。数据被包含在请求体中。POST请求可能会导致新的资源的建立和/或已有资源的修改。 Load runner中对应POST请求函数：web_submit_data,web_submit_form
    5、PUT
        向指定资源位置上传其最新内容
    6、DELETE
        请求服务器删除Request-URL所标识的资源
    7、TRACE
        回显服务器收到的请求，主要用于测试或诊断
    8、CONNECT
        HTTP/1.1协议中预留给能够将连接改为管道方式的代理服务器。
    注意：
        1）方法名称是区分大小写的，当某个请求所针对的资源不支持对应的请求方法的时候，服务器应当返回状态码405（Mothod Not Allowed）；当服务器不认识或者不支持对应的请求方法时，应返回状态码501（Not Implemented）。
        2）HTTP服务器至少应该实现GET和HEAD/POST方法，其他方法都是可选的，此外除上述方法，特定的HTTP服务器支持扩展自定义的方法。
 * */

class CurlRequest
{
    const OPTIONS_METHOD = 'OPTIONS';
    const GET_METHOD     = 'GET';
    const POST_METHOD    = 'POST';
    const HEAD_METHOD    = 'HEAD';
    const PUT_METHOD     = 'PUT';
    const DELETE_METHOD  = 'DELETE';
    const TRACE_METHOD   = 'TRACE';
    const CONNECT_METHOD = 'CONNECT';

    const PATCH_METHOD = 'PATCH';

    private $ch         = null;
    private $headers    = [
        'Accept-Encoding: gzip, deflate',
    ];
    private $params     = [];
    private $retryTimes = 1;//重试次数


    public function __construct()
    {
        $this->ch = curl_init();
    }

    /**
     * 设置重试次数
     * @param int $count
     * @return $this
     */
    public function setRetryTimes(int $count)
    {
        if ($count > 1) {
            $this->retryTimes = $count;
        }
        return $this;
    }

    /**
     * 设置http header
     * @param $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        if (is_array($headers)) {
            $this->headers = array_merge($this->headers, $headers);
        }
        return $this;
    }

    /**
     * 设置http连接超时
     * @param int $time 毫秒
     * @return $this
     */
    public function setConnectTimeout(int $time)
    {
        // 不能小于等于0
        if ($time <= 0) {
            $time = 500;
        }
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT_MS, $time);
        return $this;
    }

    /**
     * 设置http执行超时
     * @param int $time 毫秒
     * @return $this
     */
    public function setTimeout(int $time)
    {
        // 不能小于等于0
        if ($time <= 0) {
            $time = 500;
        }
        curl_setopt($this->ch, CURLOPT_TIMEOUT_MS, $time);
        return $this;
    }

    /**
     * 指定HTTP重定向的最多数量
     * @param $max_redirs
     */
    public function maxRedirs(int $max_redirs)
    {
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $max_redirs);
    }


    /**
     * 设置http 代理
     * @param $ip
     * @param $port
     * @return $this
     */
    public function setProxy($ip, $port)
    {
        if (!empty($ip) && !empty($port)) {
            curl_setopt($this->ch, CURLOPT_PROXY, $ip);
            curl_setopt($this->ch, CURLOPT_PROXYPORT, $port);
        }
        return $this;
    }

    /**
     * 设置来源页面
     * @param string $referer
     * @return $this
     */
    public function setReferer($referer = "")
    {
        if (!empty($referer)) {
            curl_setopt($this->ch, CURLOPT_REFERER, $referer);
        }
        return $this;
    }

    /**
     * 模拟用户使用的浏览器
     * @param string $agent
     * @return $this
     */
    public function setUserAgent($agent = "")
    {
        if ($agent) {
            curl_setopt($this->ch, CURLOPT_USERAGENT, $agent);
        }
        return $this;
    }

    /**
     * http响应中是否显示header，1/true表示显示
     * @param $show
     * @return $this
     */
    public function showHeader($show)
    {
        curl_setopt($this->ch, CURLOPT_HEADER, $show);
        return $this;
    }


    /**
     * 设置http请求的参数,get或post
     * @param array $params
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * 发起请求
     * @param string $url    请求URL
     * @param string $method 请求方法[GET,POST,PUT,DELETE,]
     * @return mixed|string
     * @throws Exception
     */
    public function request(string $url, $method = self::GET_METHOD)
    {
        if (!in_array($method, [
            self::GET_METHOD,
            self::POST_METHOD,
            self::PUT_METHOD,
            self::HEAD_METHOD,
            self::DELETE_METHOD,
            self::OPTIONS_METHOD,
            self::TRACE_METHOD,
            self::CONNECT_METHOD,
        ])) {
            throw new Exception('The request method is illegal', 00);
        }
        if ($method == self::GET_METHOD) {
            $params = http_build_query($this->params);
            strpos($url, '?') === false ? $url .= '?' : $url .= '&';
            $url .= $params;
            curl_setopt($this->ch, CURLOPT_HTTPGET, TRUE);
            curl_setopt($this->ch, CURLOPT_POST, FALSE);
        } else {
            curl_setopt($this->ch, CURLOPT_HTTPGET, FALSE);
            if ($method !== self::POST_METHOD) {
                $this->headers[] = 'Content-type:application/json';
                if (is_array($this->params)) {
                    $params = json_encode($this->params, JSON_UNESCAPED_UNICODE);
                } else {
                    $params = $this->params;
                }
            } else {
                $params = http_build_query($this->params);
            }
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
        }
        if (!empty($this->headers)) {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers);
        }
        if (stripos($url, 'https://') !== FALSE) {
            // https请求 不验证证书和host
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($this->ch, CURLOPT_SSLVERSION, 1);
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip,deflate');

        $tryTimes = 1;
        $content  = "";
        while ($tryTimes <= $this->retryTimes) {
            $content    = curl_exec($this->ch);
            $costTime   = curl_getinfo($this->ch, CURLINFO_TOTAL_TIME);//花费时间
            $httpCode   = curl_getinfo($this->ch, CURLINFO_HTTP_CODE); // http状态码
            $realUrl    = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL); // 请求的真实URL
            $headerSize = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE); // header 大小
            $errNo      = curl_errno($this->ch);// 错误码
            $errMsg     = curl_error($this->ch); // 错误信息

            if ($errNo == 0) {
//                curl_reset($this->ch);
                break;
            } else {
                // 出错
                throw new Exception($errMsg, $errNo);
            }
            $tryTimes++;
        }
        return compact('httpCode', 'realUrl', 'headerSize', 'errNo', 'errMsg', 'costTime', 'content');
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }
}


// 使用
$curl = new CurlRequest();

$app_id = 3321;

$params = [
    'app_poi_codes' => 't346345e',
    'app_id'        => $app_id,
    'timestamp'     => time()
];

$url = 'http://10.188.40.24:8011/api/account/user/test';
try {
    $res1 = $curl->setParams($params)->setRetryTimes(3)->request($url, CurlRequest::OPTIONS_METHOD);
} catch (Exception $e) {
    die('Exception: ' . $e->getMessage());
}

$content = $res1['content'];
var_dump($content);
var_dump(json_decode($content, true));