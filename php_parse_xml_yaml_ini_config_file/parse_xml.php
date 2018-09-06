<?php
/**
 * Desc:
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/9/6
 * Time: 上午10:50
 */
$xml      = simplexml_load_file('./app.xml');
$conf_arr = json_decode(json_encode($xml, JSON_UNESCAPED_UNICODE), true);
var_dump($conf_arr);
