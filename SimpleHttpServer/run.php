<?php
/**
 * Desc: 开始运行
 * User: baagee
 * Date: 2018/12/24
 * Time: 下午3:52
 */

include_once __DIR__ . '/vendor/autoload.php';

$webroot      = __DIR__ . '/App';
$ip           = '0.0.0.0';
$port         = 8934;
$http_log_dir = __DIR__ . '/log';
try {
    $s = new \SimServer\HttpServer($webroot, $ip, $port, $http_log_dir);
    $s->run();
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage();
}
