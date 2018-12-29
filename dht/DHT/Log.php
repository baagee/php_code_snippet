<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/27
 * Time: 下午9:50
 */

namespace DHT;
class Log
{
    private const LOG_FILE = __DIR__ . '/log.log';

    public static function log($str)
    {
        $str = sprintf('[%s] %s' . PHP_EOL, date('Y-m-d H:i:s'), $str);
        echo $str;
        file_put_contents(self::LOG_FILE, $str, FILE_APPEND | LOCK_EX);
    }
}