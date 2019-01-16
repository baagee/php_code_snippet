<?php
/**
 * Desc:
 * User: baagee()
 * Date: 2018/9/6
 * Time: 下午12:22
 */
// 详情：https://blog.csdn.net/zhang197093/article/details/75094816
error_reporting(0);

class ExceptionErrorHandle
{
    protected static $err_file = '';
    protected static $err_line = 0;
    protected static $err_msg  = '';
    protected static $err_code = 0;
    public const ERROR_TYPE_ARRAY = array(
        E_ERROR             => 'Fatal Error',
        E_WARNING           => 'Warning',//
        E_PARSE             => 'Parse Error',
        E_NOTICE            => 'Notice',//
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Strict',//
        E_RECOVERABLE_ERROR => 'Recoverable Error',//
        E_DEPRECATED        => 'Deprecated',//
        E_USER_DEPRECATED   => 'User Deprecated'
    );

    protected static function getErrorType($err_no)
    {
        return self::ERROR_TYPE_ARRAY[$err_no] ?? 'Unknow Error';
    }

    protected static function exceptionHandle(Throwable $exception)
    {
        if ($exception instanceof Error) {
            if ($prev = $exception->getPrevious()) {
                if ($prev instanceof ErrorException) {
                    // 错误来自errorHandler抛出的，获取真实错误文件信息
                    self::$err_file = $prev->getFile();
                    self::$err_line = $prev->getLine();
                }
            }
            // 记录Log
        }
        if (self::$err_file == '' || self::$err_line == 0) {
            self::$err_file = $exception->getFile();
            self::$err_line = $exception->getLine();
        }
        if ($exception instanceof Error) {
            echo 'ErrorType: ' . self::getErrorType($exception->getCode()) . PHP_EOL;
        }
        echo 'File: ' . self::$err_file . PHP_EOL;
        echo 'Line: ' . self::$err_line . PHP_EOL;
        echo 'Code: ' . $exception->getCode() . PHP_EOL;
        echo 'Message: ' . $exception->getMessage() . PHP_EOL;
        // getPHPCode
        // show
        die;
    }

    protected static function errorHandle($err_no, $err_msg, $err_file, $err_line)
    {
        throw new Error($err_msg, $err_no, new ErrorException($err_msg, $err_no, 1, $err_file, $err_line));
    }

    public static function register()
    {
        set_error_handler(function ($err_no, $err_msg, $err_file, $err_line) {
            self::errorHandle($err_no, $err_msg, $err_file, $err_line);
        });
        set_exception_handler(function (Throwable $exception) {
            self::exceptionHandle($exception);
        });
    }
}

ExceptionErrorHandle::register();


function printErrorException($err_msg, $err_file, $err_line, $err_code)
{
    echo 'File: ' . $err_file . PHP_EOL;
    echo 'Line: ' . $err_line . PHP_EOL;
    echo 'Code: ' . $err_code . PHP_EOL;
    echo 'Message: ' . $err_msg . PHP_EOL;
}


function error_log_($err_msg, $err_file, $err_line, $err_code, $err_type)
{
    echo 'Error Type: ' . $err_type . PHP_EOL;
    echo 'File: ' . $err_file . PHP_EOL;
    echo 'Line: ' . $err_line . PHP_EOL;
    echo 'Code: ' . $err_code . PHP_EOL;
    echo 'Message: ' . $err_msg . PHP_EOL;
}

function getErrorType($err_code)
{
    return ExceptionErrorHandle::ERROR_TYPE_ARRAY[$err_code] ?? '';
}
fgsfsd();
// include_once './error.php';

try {
    aaa();
    // new adfa();
    include_once './error.php';
} catch (Throwable $e) {
    $err_msg  = $e->getMessage();
    $err_code = $e->getCode();
    $err_file = $e->getFile();
    $err_line = $e->getLine();
    if ($e instanceof Error) {
        if ($prev = $e->getPrevious()) {
            if ($prev instanceof ErrorException) {
                $err_file = $prev->getFile();
                $err_line = $prev->getLine();
            }
        }
        $err_type = getErrorType($err_code);
        if ($err_type == '') {
            if (strtolower(get_class($e)) == 'parseerror') {
                $err_type = 'Parse Error';
            } else {
                $err_type = 'Fatal Error';
            }
        }
        error_log_($err_msg, $err_file, $err_line, $err_code, $err_type);
    } else {
        printErrorException($e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode());
    }
}