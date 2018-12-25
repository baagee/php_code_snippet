<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/25
 * Time: 下午1:53
 */

namespace SimServer;
/**
 * Class ServerLog
 * @package SimServer
 */
class ServerLog
{
    /**
     * log文件名
     */
    private const LOG_FILE = 'http.log';
    /**
     * @var string log目录
     */
    private static $log_dir = '';

    /**
     *  初始化，创建log目录
     * @param $log_dir
     */
    public static function init($log_dir)
    {
        $http_log_dir = realpath($log_dir);
        if (!is_dir($http_log_dir)) {
            mkdir($http_log_dir);
        }
        self::$log_dir = $http_log_dir;
    }

    /**
     *记录Log
     * @param string $msg log信息
     */
    public static function record($msg)
    {
        $str = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
        echo $str;
        $file = self::$log_dir . DIRECTORY_SEPARATOR . date('Y-m-d-H') . '-' . self::LOG_FILE;
        file_put_contents($file, $str, FILE_APPEND | LOCK_EX);
    }
}