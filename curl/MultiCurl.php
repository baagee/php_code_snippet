<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/8
 * Time: 上午10:57
 */
// 循环多次请求接口
$start_time = microtime(true);
$res        = [];
$url        = 'http://localhost:8080/test/cache';
for ($i = 0; $i < 10; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res[$i] = curl_exec($ch);
}
//var_dump($res);
echo 'time=' . (microtime(true) - $start_time) . PHP_EOL;//time=0.36159300804138

//muti_curl

$start_time = microtime(true);
$ch_arr     = [];
for ($i = 0; $i < 10; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ch_arr[$i] = $ch;
}
$mh = curl_multi_init();
foreach ($ch_arr as $ch) {
    curl_multi_add_handle($mh, $ch);
}
$runing = 1;
do {
    curl_multi_exec($mh, $runing);
    //系统会不停地执行curl_multi_exec()函数。这样可能会轻易导致CPU占用很高
} while ($runing > 0);
$res = [];
foreach ($ch_arr as $ch) {
    $res[] = curl_multi_getcontent($ch);
    curl_multi_remove_handle($mh, $ch);
}

curl_multi_close($mh);
//var_dump($res);
echo 'time=' . (microtime(true) - $start_time) . PHP_EOL;//time=0.12072205543518


// 并发优化CPU占用高
$start_time = microtime(true);
$ch_arr     = [];
for ($i = 0; $i < 10; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ch_arr[$i] = $ch;
}
$mh = curl_multi_init();
foreach ($ch_arr as $ch) {
    curl_multi_add_handle($mh, $ch);
}
$runing = 1;

do {
    $mc = curl_multi_exec($mh, $runing);
//当返回值等于CURLM_CALL_MULTI_PERFORM时，表明数据还在写入或读取中，执行循环，
//当第一次$ch句柄的数据写入或读取成功后，返回值变为CURLM_OK，跳出本次循环，进入下面的大循环之中
} while ($mc == CURLM_CALL_MULTI_PERFORM);

while ($runing && $mc == CURLM_OK) {
    //阻塞直到cURL批处理连接中有活动连接。成功时返回描述符集合中描述符的数量。失败时，select失败时返回-1
    if (curl_multi_select($mh) != -1) {
        //$mh批处理中还有可执行的$ch句柄，curl_multi_select($mh) != -1程序退出阻塞状态。
        do {
            //有活动连接时执行
            $mc = curl_multi_exec($mh, $runing);
        } while ($mc == CURLM_CALL_MULTI_PERFORM);
    }
}

$res = [];
foreach ($ch_arr as $ch) {
    $res[] = curl_multi_getcontent($ch);
    curl_multi_remove_handle($mh, $ch);
}

curl_multi_close($mh);
//var_dump($res);
echo 'time=' . (microtime(true) - $start_time) . PHP_EOL;//time=0.11989092826843

//还存在优化的空间, 当某个URL请求完毕之后尽可能快的去处理它, 边处理边等待其他的URL返回,
// 而不是等待那个最慢的接口返回之后才开始处理等工作, 从而避免CPU的空闲和浪费

$res        = [];
$start_time = microtime(true);
//$ch_arr     = [];
$mh = curl_multi_init();
for ($i = 0; $i < 10; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    $ch_arr[$i] = $ch;
    curl_multi_add_handle($mh, $ch);
}

$runing = 1;

do {
    $mc = curl_multi_exec($mh, $runing);
    while ($done = curl_multi_info_read($mh)) {

//        $info                                     = curl_getinfo($done['handle']);
//        $error                                    = curl_error($done['handle']);
        $results = curl_multi_getcontent($done['handle']);
        $res[]   = $results;
//        echo 'curl_multi_getcontent' . PHP_EOL;

        curl_multi_remove_handle($mh, $done['handle']);
        curl_close($done['handle']);
    }
} while ($runing);

curl_multi_close($mh);
//var_dump($res);
echo 'time=' . (microtime(true) - $start_time) . PHP_EOL;
/*
 * time=0.31974291801453
time=0.12140798568726
time=0.11273312568665
time=0.10329294204712
 * */

function multi_post($arrRequests)
{
    $multiCurlPool = [];
    $arrResponse   = [];

    if (empty($arrRequests)) {
        return false;
    }

    $multi = curl_multi_init();

    //装载每一个请求
    foreach ($arrRequests as $reqKey => $request) {
        $multiCurlPool[$reqKey] = curl_init();
        curl_setopt($multiCurlPool[$reqKey], CURLOPT_POST, 1);
        curl_setopt($multiCurlPool[$reqKey], CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($multiCurlPool[$reqKey], CURLOPT_HTTPHEADER, array("Content-type: application/json; charset=utf-8", "Accept-Encoding: gzip"));
        curl_setopt($multiCurlPool[$reqKey], CURLOPT_POSTFIELDS, json_encode($request));
        curl_setopt($multiCurlPool[$reqKey], CURLOPT_USERAGENT, "eleme-openapi-php-sdk");

//        $proxyInfo = $this->getProxyServer();
//        if (empty($proxyInfo)) {
//            \Sftcwl\Log\RpcLog::warning('service config need http proxy; but proxy ip empty');
//        } else {
//            curl_setopt($multiCurlPool[$reqKey], CURLOPT_PROXY, $proxyInfo["ip"]); //代理服务器地址
//            curl_setopt($multiCurlPool[$reqKey], CURLOPT_PROXYPORT, $proxyInfo["port"]); //代理服务器端口
//            curl_setopt($multiCurlPool[$reqKey], CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
//        }
        curl_setopt($multiCurlPool[$reqKey], CURLOPT_TIMEOUT, 10);
        curl_setopt($multiCurlPool[$reqKey], CURLOPT_ENCODING, "gzip");

        //加入句柄队列
        curl_multi_add_handle($multi, $multiCurlPool[$reqKey]);
    }

    $active = null;
    do {
        //开始发送请求
        while (($mrc = curl_multi_exec($multi, $active)) == CURLM_CALL_MULTI_PERFORM) ;
        if ($mrc != CURLM_OK) {
            return null;
        }

        //读取已有响应的socket
        while ($done = curl_multi_info_read($multi)) {
            //找到当前有响应的资源句柄，对应的请求
            $reqKey = array_search($done['handle'], $multiCurlPool);

            //multi_curl 通过curl_errno没有办法拿到状态码
            $err   = curl_error($done['handle']);
            $errno = $done['result'];

            if ($errno == 0) {
                $content = curl_multi_getcontent($done['handle']);
                \Sftcwl\Log\RpcLog::notice("eleme_sdk request ok ");

            } else {
                $content = false;
                \Sftcwl\Log\RpcLog::warning("eleme_sdk request error: [" . $errno . ']' . $err);

            }

            curl_multi_remove_handle($multi, $done['handle']);
            curl_close($done['handle']);

            $arrResponse[$reqKey] = $content;
        }

    } while ($active);

    curl_multi_close($multi);

    ksort($arrResponse);

    return $arrResponse;
}