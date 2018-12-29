<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/27
 * Time: 下午3:28
 */
include_once __DIR__ . '/vendor/autoload.php';

class RunSpider
{
    public function run()
    {
        (new \DHT\DHTSpider([]))->start();
    }
}


(new RunSpider())->run();