<?php
/**
 * Desc:
 * User: baagee()
 * Date: 2018/9/6
 * Time: 下午12:22
 */

// 详情：https://blog.csdn.net/zhang197093/article/details/75094816

class ExceptionErrorHandle
{
    protected static function _handle($err_no, $err_msg, $err_file, $err_line, $is_error)
    {
        echo PHP_EOL;
        if ($is_error) {
            $tmp = '错误';
        } else {
            $tmp = '异常';
        }
        echo $tmp . '码：' . $err_no . PHP_EOL;
        echo $tmp . '信息：' . $err_msg . PHP_EOL;
        echo $tmp . '文件：' . $err_file . PHP_EOL;
        echo $tmp . '行：' . $err_line . PHP_EOL;
//        var_dump(debug_backtrace());
    }

    public static function exceptionHandle(Throwable $exception)
    {
        $is_error = false;
        if ($exception instanceof Error) {
            $is_error = true;
        }
        self::_handle($exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine(), $is_error);
    }

    public static function errorHandle($err_no, $err_msg, $err_file, $err_line)
    {
        self::_handle($err_no, $err_msg, $err_file, $err_line, true);
    }
}

set_error_handler(function ($err_no, $err_msg, $err_file, $err_line) {
    ExceptionErrorHandle::errorHandle($err_no, $err_msg, $err_file, $err_line);
});
set_exception_handler(function ($exception) {
    ExceptionErrorHandle::exceptionHandle($exception);
});

echo $a['dfs'];
trigger_error('自定义错误');
//fasdfsa();
throw new Exception('抛出异常',1111);
