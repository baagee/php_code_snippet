<?php
/**
 * Desc: 开始运行
 * User: baagee
 * Date: 2018/12/24
 * Time: 下午3:52
 */

include_once __DIR__ . '/vendor/autoload.php';

$webroot       = __DIR__ . '/App';
$ip            = '127.0.0.1';
$port          = 8888;
$http_log_file = __DIR__ . '/log/http.log';
try {
    $s = new \SimServer\HttpServer($webroot, $ip, $port, $http_log_file);
    $s->run();
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage();
}
