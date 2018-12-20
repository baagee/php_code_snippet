<?php
/**
 * Desc: 异常和错误显示 注意：ROOT_PATH常量请先定义，为项目根目录
 * 注意：ROOT_PATH常量请先定义，为项目根目录
 * 注意：ROOT_PATH常量请先定义，为项目根目录
 * 注意：ROOT_PATH常量请先定义，为项目根目录
 * 注意：ROOT_PATH常量请先定义，为项目根目录
 * User: baagee()
 * Date: 2018/9/6
 * Time: 下午3:24
 */


class ExceptionErrorHandler
{
    // 存放代码回溯信息
    protected static $backtrace = [];
    // 错误类型 error exception
    protected static $type = 'Error';
    // 错误信息
    protected static $err_msg = '';
    // 错误行
    protected static $err_line = 0;
    // 保存错误的php代码
    protected static $err_php_code_arr = [];

    protected static $error_log = '';

    protected static $is_debug = true;

    /**
     * 注册错误异常处理机制
     * @param $err_log_file
     */
    public static function register($err_log_file, $is_debug = true)
    {
        self::$error_log = $err_log_file;
        self::$is_debug  = $is_debug;
        set_error_handler(function ($err_no, $err_msg, $err_file, $err_line) {
            self::errorHandle($err_no, $err_msg, $err_file, $err_line);
        });
        set_exception_handler(function ($exception) {
            self::exceptionHandle($exception);
        });
    }

    /**
     * 展示错误信息界面
     */
    protected static function show()
    {
        $errorMsg         = str_replace(ROOT_PATH, '', self::$err_msg);
        $title_type       = self::$type;
        $backtrace        = self::$backtrace;
        $err_php_code_arr = self::$err_php_code_arr;
        $is_debug         = self::$is_debug;
        $err_line         = self::$err_line;
        if (strtolower(php_sapi_name()) !== 'cli') {
            $host = $_SERVER['HTTP_HOST'];
            ob_end_clean();
            header("Content-type: text/html; charset=utf-8");
            include_once __DIR__ . '/error_tpl.php';
        } else {
            echo sprintf(PHP_EOL . "\033[1;37;41m%s\033[0m : \033[1;31m%s\033[0m" . PHP_EOL . PHP_EOL, $title_type, $errorMsg);
            $end_trace = $backtrace[0];
            echo sprintf("at \033[1;37;42m%s : %d\033[0m " . PHP_EOL, ROOT_PATH . $end_trace['file'], $end_trace['line']);
            $max_len = strlen(max(array_keys($err_php_code_arr)));
            foreach ($err_php_code_arr as $line => $code) {
                $line = str_pad($line, $max_len, '0', STR_PAD_LEFT);
                if (!empty($code)) {
                    $code = htmlspecialchars_decode($code);
                    if ($err_line == $line) {
                        echo sprintf("%s: \033[1;37;41m%s\033[0m" . PHP_EOL, $line, rtrim($code, PHP_EOL));
                    } else {
                        echo sprintf("%s: %s", $line, $code);
                    }
                }
            }
            echo PHP_EOL;
        }
        exit();
    }

    /**
     * 记录php错误信息
     * @param string $err_file 出错文件
     * @param int    $err_line 出错行数
     * @param string $err_msg  出错信息
     */
    protected static function recordError($err_file, $err_line, $err_msg)
    {
        $path             = dirname(self::$error_log);
        if (!is_dir($path)) {
            exec('mkdir -p ' . $path);
        }
        // [time] [type] [file:line] [error_message]
        $log_str = sprintf('[%s] [%s] [%s:%d] %s' . PHP_EOL,
            date('Y-m-d H:i:s'), self::$type, str_replace(ROOT_PATH, '', $err_file), $err_line, $err_msg);
        error_log($log_str, 3, self::$error_log);
    }

    /**
     * 异常处理函数
     * @param \Throwable $exception
     * @throws \Throwable
     */
    protected static function exceptionHandle(\Throwable $exception)
    {
        if ($exception instanceof \ErrorException) {
        } else {
            self::$type = get_class($exception);
            if (strtolower(substr($exception->getMessage(), 0, 3)) == 'sql') {
                self::$type = 'SQLError';
            }
        }
        $backtrace      = $exception->getTrace();
        self::$err_line = $exception->getLine();
        array_unshift($backtrace, array('file' => $exception->getFile(), 'line' => self::$err_line, 'function' => 'break'));
        self::$backtrace = [];
        foreach ($backtrace as $error) {
            if (!empty($error['function'])) {
                $fun = '';
                if (!empty($error['class'])) {
                    $fun .= $error['class'] . $error['type'];
                }
                $fun               .= $error['function'] . '([args])';
                $error['function'] = $fun;
            }
            if (!isset($error['line'])) {
                continue;
            }
            if (!empty($error['file']) && !empty($error['line'])) {
                self::$backtrace[] = array('file' => str_replace(array(ROOT_PATH, '\\'), array('', '/'), $error['file']), 'line' => $error['line'], 'function' => $error['function']);
            }
        }
        self::$err_msg = '[' . $exception->getCode() . '] ' . $exception->getMessage();

        self::getPhpCode();
        self::show();
    }

    /**
     * 获取php出错代码附近的代码
     */
    protected static function getPhpCode()
    {
        $error_file = ROOT_PATH . self::$backtrace[0]['file'];
        $err_line   = self::$backtrace[0]['line'];
        $fh         = new \SplFileObject($error_file);
        $start_line = $err_line - 9 < 0 ? 0 : $err_line - 9;
        $fh->seek($start_line);
        $content = [];
        for ($i = 0; $i <= 16; ++$i) {
            $content[$start_line + $i + 1] = htmlspecialchars($fh->current());
            $fh->next();
        }
        self::$err_php_code_arr = $content;
    }

    /**
     * 错误处理函数
     * @param int    $err_no   错误码
     * @param string $err_msg  错误信息
     * @param string $err_file 错误文件
     * @param int    $err_line 错误行
     * @throws \ErrorException
     */
    protected static function errorHandle($err_no, $err_msg, $err_file, $err_line)
    {
        self::$type = self::getErrorType($err_no);
        // 记录错误信息
        self::recordError($err_file, $err_line, $err_msg);
        throw new \ErrorException($err_msg, $err_no, 0, $err_file, $err_line);
    }

    /**
     * 由错误码转换错误类型
     * @param $err_no
     * @return string
     */
    private static function getErrorType($err_no)
    {
        switch ($err_no) {
            case E_WARNING:
                $level_tips = 'PHP Warning';
                break;
            case E_NOTICE:
                $level_tips = 'PHP Notice';
                break;
            case E_DEPRECATED:
                $level_tips = 'PHP Deprecated';
                break;
            case E_USER_ERROR:
                $level_tips = 'User Error';
                break;
            case E_USER_WARNING:
                $level_tips = 'User Warning';
                break;
            case E_USER_NOTICE:
                $level_tips = 'User Notice';
                break;
            case E_USER_DEPRECATED:
                $level_tips = 'User Deprecated';
                break;
            case E_STRICT:
                $level_tips = 'PHP Strict';
                break;
            default:
                $level_tips = 'Unknow Type Error';
                break;
        }
        return $level_tips;
    }
}
