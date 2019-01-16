<?php

/**
 * Desc:
 * User: baagee
 * Date: 2018/11/27
 * Time: 下午1:46
 */
class Event
{
    /**
     *  保存监听的时间列表
     * @var array
     */
    protected static $listens = [];

    /**
     * 添加监听时间
     * @param string   $event    事件名字
     * @param callback $callback 处理函数
     * @param bool     $once     是否触发一次
     * @return bool
     */
    public static function listen(string $event, callable $callback, bool $once = false)
    {
        if (!is_callable($callback)) {
            return false;
        }
        self::$listens[$event][] = ['callback' => $callback, 'once' => $once];
        var_dump(self::$listens);
        return true;
    }

    /**
     * 删除事件
     * @param string $event 事件名字
     * @param null   $index 相同事件名字下的
     */
    public static function remove($event, $index = null)
    {
        if (is_null($index)) {
            unset(self::$listens[$event]);
        } else {
            unset(self::$listens[$event][$index]);
        }
    }

    /**
     * 触发器
     * @return bool|mixed
     */
    public static function trigger()
    {
        if (!func_num_args()) {
            return false;
        }
        //获取方法的参数
        $args = func_get_args();
        // 提取事件名字
        $event = array_shift($args);
        if (!isset(self::$listens[$event])) {
            return false;
        }
        $res = [];
        foreach ((array)self::$listens[$event] as $index => $listen) {
            $callback = $listen['callback'];
            if ($listen['once']) {
                // 如果事件监听一次 一次触发就删除
                self::remove($event, $index);
            }
            // 调用监听处理函数
            $res[$index] = call_user_func_array($callback, $args);
        }
        return $res;
    }
}

// 增加监听walk事件 匿名函数
// Event::listen('walk', function () {
//     echo "I am walking...\n";
//     return 'res1';
// });
// // 增加监听walk一次性事件
// Event::listen('walk', function () {
//     echo "I am listening...\n";
//     return 'res2';
// }, true);
// // 触发walk事件
// $res = Event::trigger('walk');
// var_dump($res);

/*
I am walking...
I am listening...
*/
//Event::trigger('walk');
/*
I am walking...
*/

Event::listen('say', function ($name = '') {
    echo "I am {$name}\n";
}, true);
//
Event::trigger('say', 'deeka'); // 输出 I am deeka
Event::trigger('say', 'weeee'); // not run

die;
//
class Foo
{
    public function bar()
    {
        echo "Foo::bar() is called\n";
    }

    public function test()
    {
        echo "Foo::foo() is called, agrs:" . json_encode(func_get_args()) . "\n";
    }
}

$foo = new Foo;

Event::listen('bar', array($foo, 'bar'));
Event::trigger('bar');
//
Event::listen('test', array($foo, 'test'));
Event::trigger('test', 1, 2, 3);

//
class Bar
{
    public static function foo()
    {
        echo "Bar::foo() is called\n";
    }
}

Event::listen('bar1', array('Bar', 'foo'));
Event::trigger('bar1');
//
Event::listen('bar2', 'Bar::foo');
Event::trigger('bar2');

function bar()
{
    echo "bar() is called\n";
}

Event::listen('bar3', 'bar');
Event::trigger('bar3');