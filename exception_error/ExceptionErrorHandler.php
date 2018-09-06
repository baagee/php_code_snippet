<?php
/**
 * Desc: 异常和错误显示 注意：ROOT_PATH常量请先定义，为项目根目录
 * 注意：ROOT_PATH常量请先定义，为项目根目录
 * 注意：ROOT_PATH常量请先定义，为项目根目录
 * 注意：ROOT_PATH常量请先定义，为项目根目录
 * 注意：ROOT_PATH常量请先定义，为项目根目录
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/9/6
 * Time: 下午3:24
 */

/*
 * 使用：
 * set_error_handler(function ($err_no, $err_msg, $err_file, $err_line) {
        ExceptionErrorHandler::errorHandle($err_no, $err_msg, $err_file, $err_line);
    });
    set_exception_handler(function ($exception) {
        ExceptionErrorHandler::exceptionHandle($exception);
    });
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

    /**
     * 展示错误信息界面
     */
    protected static function show()
    {
        $errorMsg         = str_replace(ROOT_PATH, '', self::$err_msg);
        $host             = $_SERVER['HTTP_HOST'];
        $title_type       = self::$type;
        $backtrace        = self::$backtrace;
        $err_php_code_arr = self::$err_php_code_arr;
        $is_debug         = true;
        $err_line         = self::$err_line;
        ob_end_clean();
        include_once __DIR__ . '/error_tpl.php';
        exit();
    }

    /**
     * 异常处理函数
     * @param \Throwable $exception
     */
    public static function exceptionHandle(\Throwable $exception)
    {
        if ($exception instanceof \Exception) {
            self::$type = 'Exception';
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
            self::$backtrace[] = array('file' => str_replace(array(ROOT_PATH, '\\'), array('', '/'), $error['file']), 'line' => $error['line'], 'function' => $error['function']);
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
        $fh         = new SplFileObject($error_file);
        $start_line = $err_line - 9 < 0 ? 0 : $err_line - 9;
        $fh->seek($start_line);
        $content = [];
        for ($i = 0; $i <= 16; ++$i) {
            $content[self::getFormatLineNumber($start_line + $i + 1)] = htmlspecialchars($fh->current());
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
     */
    public static function errorHandle($err_no, $err_msg, $err_file, $err_line)
    {
        self::$type     = 'Error';
        self::$err_line = $err_line;
        self::$err_msg  = '[' . $err_no . '] ' . $err_msg;
        $debugBacktrace = debug_backtrace();
        $backtrace      = [];
        ksort($debugBacktrace);
        foreach ($debugBacktrace as $k => $error) {
            if ($k === 0) {
                continue;
            }
            $tmp = [];
            if (!isset($error['file'])) {
                // 利用反射API来获取方法/函数所在的文件和行数
                try {
                    if (isset($error['class'])) {
                        $reflection      = new \ReflectionMethod($error['class'], $error['function']);
                        $tmp['function'] = $error['class'] . $error['type'] . $error['function'] . '([args])';
                    } else {
                        $reflection      = new \ReflectionFunction($error['function']);
                        $tmp['function'] = $error['function'] . '([args])';
                    }
                    $tmp['file'] = str_replace(ROOT_PATH, '', $reflection->getFileName());
                    $tmp['line'] = $reflection->getStartLine();
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                $tmp['file']     = str_replace(ROOT_PATH, '', $error['file']);
                $tmp['line']     = $error['line'];
                $func            = isset($error['class']) ? $error['class'] : '';
                $func            .= isset($error['type']) ? $error['type'] : '';
                $func            .= isset($error['function']) ? $error['function'] . '([args])' : '';
                $tmp['function'] = $func;
            }
            $backtrace[] = $tmp;
        }
        self::$backtrace = $backtrace;
        self::getPhpCode();
        self::show();
    }

    /**
     * 格式化代码行数  前面补0
     * @param int $line 行数
     * @return string 格式化后的行数
     */
    protected static function getFormatLineNumber($line)
    {
        $min    = self::$err_line - 9 < 0 ? 0 : self::$err_line - 9;
        $max    = $min + 16;
        $len    = strlen(strval($max));
        $format = '%0' . $len . 'd';
        return sprintf($format, $line);
    }
}
