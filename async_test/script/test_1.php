<?php
/**
 * Created by PhpStorm.
 * User: dangliuhui
 * Date: 2018/3/12
 * Time: 下午3:48
 */
echo '脚本开始运行' . PHP_EOL;
$s_t = microtime(true);
$fields = [
    'name:',
    'age:',
    'aaa:',
    'bbb:',
    'ccc:',
];
//php cli模式下接收脚本参数
$where = getopt('', $fields);

var_dump('接收到的参数：' . json_encode($where, JSON_UNESCAPED_UNICODE));
foreach ($where as $k => $v) {
    echo sprintf('%s=>%s', $k, $v) . PHP_EOL;
    sleep(1);
}

$e_t = microtime(true);
echo '脚本执行结束,运行时间:' . ($e_t - $s_t);
