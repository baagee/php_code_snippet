<?php
/**
 * Created by PhpStorm.
 * User: dangliuhui
 * Date: 2018/3/12
 * Time: 下午5:18
 */
include './AsyncTask.php';
//test
$data = [
    'name' => 'hahha哈哈哈',
    'age' => 123,
    'aaa' => 'jkhgf9876',
    'bbb' => 'bbbbb',
    'ccc' => 'ccccccc',
];

echo 'start' . PHP_EOL;
$obj = AsyncTask::getInstance();
// 添加任务
$res = $obj->addTask('test_' . rand(1, 9), '/home/work/odp_saas_dlh/app/trade/script/async_test/script/test_1.php');
var_dump($res);
//执行任务
var_dump($obj->run('test_2', $data));
echo 'end' . PHP_EOL . PHP_EOL;