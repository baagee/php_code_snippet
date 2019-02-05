<?php
/**
 * Desc: laravel类似的中间件处理
 * User: baagee
 * Date: 2019/1/15
 * Time: 下午5:22
 */

$param1 = [1, 2, 3];
$param3 = 9;
$res=array_reduce($param1, function ($last, $item) {
    $last += $item;
    return $last;
}, $param3);
// var_dump($res);
/**
 * array_reduce
 *  param1 array
 *  param2 callable function ($lastValue,$param1ItemValue)
 *          $lastValue 上次迭代回调函数里的值； 如果本次迭代是第一次，那么这个值是 param3
 *  param3 可选，default null，初始化的$lastValue值
 */


// 等价于

function sum($last, $item)
{
    $last += $item;
    return $last;
}
foreach ($param1 as $param1ItemValue) {
    $param3 = sum($param3, $param1ItemValue);
}

// var_dump($param3);
// die;
// ************************************************************************************

/**
 * Class MidAbs
 */
abstract class MidAbs
{
    /**
     * @param Closure $next
     * @param         $data
     * @return mixed
     */
    abstract protected function handler(Closure $next, $data);

    /**
     * @param Closure $next
     * @param         $data
     * @return mixed
     */
    public function exec(Closure $next, $data)
    {
        return $this->handler($next, $data);
    }
}


/**
 * Class ReturnJson
 */
class ReturnJson extends MidAbs
{
    /**
     * @param Closure $next
     * @param         $data
     * @return mixed|void
     */
    protected function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $ret = $next($data);
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        echo json_encode($ret, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    }
}


/**
 * Class CatchError
 */
class CatchError extends MidAbs
{
    /**
     * @param Closure $next
     * @param         $data
     * @return array|mixed
     */
    protected function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $ret = [
            'code'    => 0,
            'message' => ''
        ];
        try {
            $data        .= __CLASS__ . '; ';
            $ret['data'] = $next($data);
        } catch (Throwable $e) {
            $ret = [
                'code'    => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $ret['request_id'] = time();
        return $ret;
    }
}

/**
 * Class BLogic
 */
class BLogic extends MidAbs
{
    /**
     * @param Closure $next
     * @param         $data
     * @return mixed
     */
    protected function handler(\Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $data .= __CLASS__ . '; ';
        $ret  = $next($data);
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        return $ret;
    }
}

/**
 * Class ALogic
 */
class ALogic extends MidAbs
{
    /**
     * @param Closure $next
     * @param         $data
     * @return mixed
     */
    protected function handler(Closure $next, $data)
    {
        print "开始 " . __CLASS__ . " 逻辑" . PHP_EOL;
        $data .= __CLASS__ . "; ";
        // throw new Exception(__CLASS__ . ' Exception', 100);
        $ret = $next($data);
        print "结束 " . __CLASS__ . " 逻辑" . PHP_EOL;
        return $ret;
    }
}

/**
 * Class Pipeline
 */
class Pipeline
{
    /**
     * @var string
     */
    private const MIDDLEWARE_METHOD = 'exec';

    /**
     * @var
     */
    protected $data;

    /**
     * @var array
     */
    protected $middleware_array = [];

    /**
     * 设置要处理的数据
     * @param $data
     * @return $this
     */
    public function send($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array $middleware_array 经过的中间件
     * @return $this
     */
    public function through(array $middleware_array)
    {
        $this->middleware_array = $middleware_array;
        return $this;
    }

    /**
     * @param Closure $destination 目的地
     * @return mixed
     */
    public function then(\Closure $destination)
    {
        $arrive = array_reduce(
            array_reverse($this->middleware_array),//第一个参数是反转的中间件列表
            /**
             * $stack 上一次此函数返回值 闭包
             * $middleware 此次中间件名字
             */
            function (Closure $stack, $middleware) {
                // 返回一个闭包 作为下次$stack的值进入 最后由$arrive接收
                // 循环中，每次调用回调函数就生成一个闭包匿名函数
                /**
                 * 第一次循环
                 *      $stack=$destination  $middleware=BLogic
                 *      return BLogic{$destination()}
                 *
                 * 第二次循环
                 *      $stack=BLogic{$destination()}  $middleware=ALogic
                 *      return ALogic{BLogic{$destination()}}
                 *
                 * 第三次循环
                 *      $stack=ALogic{BLogic{$destination()}}  $middleware=CatchError
                 *      return CatchError{ALogic{BLogic{$destination()}}}
                 *
                 * 第四次循环
                 *      $stack=CatchError{ALogic{BLogic{$destination()}}}  $middleware=ReturnJson
                 *      return ReturnJson{CatchError{ALogic{BLogic{$destination()}}}}
                 */
                return function ($request) use ($stack, $middleware) {
                    // 获取此中间件的对象
                    $middlewareObj = new $middleware();
                    if ($middlewareObj instanceof MidAbs) {
                        // 调用中间件
                        return $middlewareObj->{self::MIDDLEWARE_METHOD}($stack, $request);
                    } else {
                        throw new ErrorException($middleware . ' not instanceof ' . MidAbs::class);
                    }
                };
            },
            $destination
        );
        // 调用arrive
        return call_user_func($arrive, $this->data);
    }
}

// 设置要经过的中间件
$middleware = [
    ReturnJson::class,
    CatchError::class,
    ALogic::class,
    BLogic::class,
];

$request = "request data ";
(new Pipeline())->send($request)->through($middleware)->then(function ($request) {
    return [
        'time'    => time(),
        'request' => $request
    ];
});