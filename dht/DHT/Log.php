<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/27
 * Time: 下午9:50
 */

namespace DHT;
use Swoole\Process;

class Log
{
    private const LOG_FILE = __DIR__ . '/log.log';

    public static function log($str)
    {
        $str = sprintf('[%s] %s' . PHP_EOL, date('Y-m-d H:i:s'), $str);
//        echo $str;
        $process = new Process(function (Process $worker) use ($str) {
            $log_file = implode(DIRECTORY_SEPARATOR, [getcwd(), 'log', date('Y_m_d'), date('H') . '.log']);
            $log_path = dirname($log_file);
            if (!is_dir($log_path)) {
                exec('mkdir -p ' . $log_path);
            }
            file_put_contents($log_file, $str, FILE_APPEND | LOCK_EX);
            $worker->exit(0);
        }, false);
        $process->start();
    }
}