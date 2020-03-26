<?php
/**
 * Desc: mysql数据库配置
 * User: baagee
 * Date: 2019/3/15
 * Time: 下午6:47
 */

return [
    'host' => '127.0.0.1',
    'port' => 5728,
    'user' => '',
    'password' => '1q2w3e@sf',
    'database' => 'sss',
    'connectTimeout' => 1,//连接超时配置
    'charset' => 'utf8mb4',
    'retryTimes' => 1,//执行失败重试次数
    'options' => [
        //pdo连接时额外选项
        \PDO::ATTR_PERSISTENT => true,
    ],
    'schemasCachePath' => __DIR__ . '/schemas',
];
