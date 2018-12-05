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
     * @var array
     */
    protected static $listens = array();

    /**
     * @param      $event
     * @param      $callback
     * @param bool $once
     * @return bool
     */
    public static function listen(string $event, callable $callback, bool $once = false)
    {
        if (!is_callable($callback)) return false;
        self::$listens[$event][] = array('callback' => $callback, 'once' => $once);
        return true;
    }

    /**
     * @param $event
     * @param $callback
     * @return bool
     */
//    public static function one($event, $callback)
//    {
//        return self::listen($event, $callback, true);
//    }

    /**
     * @param      $event
     * @param null $index
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
     * @return bool|string
     */
    public static function trigger()
    {
        if (!func_num_args()) {
            return '';
        }
        $args = func_get_args();
        var_dump($args);
        $event = array_shift($args);

        if (!isset(self::$listens[$event])) {
            return false;
        }
        foreach ((array)self::$listens[$event] as $index => $listen) {
            $callback = $listen['callback'];
            $listen['once'] && self::remove($event, $index);
            call_user_func_array($callback, $args);
        }
    }
}

// 增加监听walk事件
Event::listen('walk', function () {
    echo "I am walking...\n";
});
// 增加监听walk一次性事件
Event::listen('walk', function () {
    echo "I am listening...\n";
}, true);
// 触发walk事件
Event::trigger('walk');
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