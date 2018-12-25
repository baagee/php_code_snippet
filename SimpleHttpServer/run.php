<?php
/**
 * Desc: 开始运行
 * User: baagee
 * Date: 2018/12/24
 * Time: 下午3:52
 */

include_once __DIR__ . '/vendor/autoload.php';

function getConf()
{
    return parse_ini_file(__DIR__ . '/conf.ini');
}

try {
    extract(getConf());
    $s = new \SimServer\HttpServer($web_root, $ip, $port, $log_dir, $main_app);
    $s->run();
} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage();
}
