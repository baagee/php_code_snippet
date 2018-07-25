<?php
/**
 * Desc: 单例模式 保证一个类仅有一个实例,并提供一个访问它的全局访问点
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/7/25
 * Time: 上午10:05
 */

class Singleton
{
    // 保存本身实例
    private static $_instance = null;

    // 禁止new
    private function __construct()
    {
    }

    // 获取示例的静态方法
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function test()
    {
        echo 'test' . PHP_EOL;
    }
}

// 不能通过new获取对象
$s = Singleton::getInstance();
$s->test();
$s1 = Singleton::getInstance();
// s 和s1是同一个对象
var_dump($s === $s1);