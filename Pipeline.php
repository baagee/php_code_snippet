<?php

/**
 * Desc:
 * User: baagee
 * Date: 2018/12/13
 * Time: 下午12:38
 */

abstract class MidAbs
{
    abstract public function handler(Closure $next, $data);

    public function exec(Closure $next, $data)
    {
        return $this->handler($next, $data);
    }
}

class Pipeline
{
    protected $method = 'exec';

    protected $data;

    protected $pipes = [];

    public function send($data)
    {
        $this->data = $data;
        return $this;
    }

    public function through($pipes)
    {
        $this->pipes = $pipes;
        return $this;
    }

    public function then(\Closure $destination)
    {
        $pipeline = array_reduce($this->pipes, function ($stack, $pipe) {
            return function ($request) use ($stack, $pipe) {
                $pipeObj = new $pipe();
                if ($pipeObj instanceof MidAbs) {
                    return $pipeObj->{$this->method}($stack, $request);
                } else {
                    die('333eee');
                }
            };
        }, $destination);
        return $pipeline($this->data);
    }
}

class ReturnJson extends MidAbs
{
    public function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $ret         = $next($data);
        $ret['time'] = time();
        echo json_encode($ret, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) . PHP_EOL;
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        die;
    }
}


class CatchError extends MidAbs
{
    public function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        try {
            $data .= __CLASS__ . ';';
            $ret  = $next($data);
        } catch (Throwable $e) {
            $ret = [
                'code'    => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        return $ret;
    }
}

class BLogic extends MidAbs
{
    public function handler(\Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $data .= __CLASS__ . ';';
        $ret  = $next($data);
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        return $ret;
    }
}

class ALogic extends MidAbs
{
    public function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $data .= __CLASS__ . ";";
        throw new Exception(__CLASS__ . ' Exception', 100);
        $ret = $next($data);
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        return $ret;
    }
}

$pipes = [
    BLogic::class,
    ALogic::class,
    CatchError::class,
    ReturnJson::class,
];

$data = "";
(new Pipeline())->send($data)->through($pipes)->then(function ($data) {
    $response = [
        'code'    => 0,
        'message' => 'success',
        'data'    => ''
    ];
    try {
        echo '开始处理Action' . PHP_EOL;
        throw new Exception('sdgdfhfgh', 90);
        $response['data'] = [
            'request' => $data,
            'time'    => time()
        ];
    } catch (Throwable $e) {
        $response['code']    = $e->getCode();
        $response['message'] = $e->getMessage();
    }
    return $response;
});
//
//function sum($last,$item){
////    $last+=$item;
////    return $last;
//}
//
//var_dump(array_reduce([1],'sum',9));