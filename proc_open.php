<?php
/**
 * Desc:
 * User: 01372412
 * Date: 2019/11/26
 * Time: 下午9:52
 */

function foo()
{
    $proc = proc_open(
        sprintf('php %s/proc_task.php', __DIR__),
        [
            0 => array('pipe', 'r'), //stdin (用fwrite写入数据给管道)
            1 => array('pipe', 'w'), //stdout(用stream_get_contents获取管道输出)
            2 => array('pipe', 'w'), //stderr(用stream_get_contents获取管道输出)
            //2 => array('file','/tmp/err.txt','a') //stderr(写入到文件)
        ],
        $pipes, //管道(stdin/stdout/stderr)
        '/tmp', //当前PHP进程的工作目录
        array('foo' => 'bar') //php.ini 配置 variables_order = "EGPCS" 其中E表示$_ENV,否则$_ENV输出为空
    );

    // var_dump($pipes); //resource of type (stream)

    if (is_resource($proc)) {
        //stdin
        $stdin = serialize(array('time' => time()));
        fwrite($pipes[0], $stdin); //把参数传给脚本task.php
        fclose($pipes[0]); //fclose关闭管道后proc_close才能退出子进程,否则会发生死锁
        register_shutdown_function(function () use ($pipes, $proc) { //事件驱动(脚本结束事件),异步回调
            //stdout
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            //stderr
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            //exit code (返回进程的终止状态码,如果发生错则返回-1)
            $status = proc_close($proc);
            $data   = array(
                'stdout' => $stdout,
                'stderr' => $stderr,
                'status' => $status,
            );
            var_export($data); //echo json_encode($data);
        });
    }
}
echo microtime(true).PHP_EOL;
foo();
echo 'OVER' . PHP_EOL;
echo microtime(true).PHP_EOL;

