<?php

/**
 * Desc: 反射结合容器依赖注入
 * User: baagee
 * Date: 2018/8/21
 * Time: 下午3:14
 */
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

class DI
{
    /**
     * @var array 保存对象
     */
    protected static $container = [];

    /**
     * 添加对象
     * @param string $k 名字
     * @param mixed  $v 类名或者匿名函数
     * @return bool 成功返回true
     * @throws Exception
     */
    final public static function set(string $k, $v)
    {
        if (isset(self::$container[$k]) && self::$container[$k]['build']) {
            throw new Exception($k . '已经存在并且实例化了');
        } else {
            self::$container[$k] = [
                'instance' => $v,// 保存实例
                'build'    => false//标记是否实例了
            ];
            return true;
        }
    }

    /**
     * @param $k
     * @return mixed|object
     * @throws Exception
     */
    final public static function get($k)
    {
        if (!isset(self::$container[$k])) {
            return null;
        } else {
            if (!self::$container[$k]['build']) {
                self::$container[$k] = [
                    'instance' => self::build(self::$container[$k]['instance']),
                    'build'    => true
                ];
            }
            return self::$container[$k]['instance'];
        }
    }

    /**
     * 获取实例
     * @param $new
     * @return mixed|object
     * @throws ReflectionException
     */
    protected static function build($new)
    {
        // 如果是匿名函数，直接执行，并返回结果
        if ($new instanceof Closure) {
            return call_user_func($new);
        }
        // 如果是类的话，使用反射加载
        $ref = new \ReflectionClass($new);
        // 监测类是否可实例化
        if (!$ref->isInstantiable()) {
            throw new Exception('class' . $new . ' not instanceable');
        }
        // 获取构造函数
        $constructor = $ref->getConstructor();
        // 无构造函数，直接实例化返回
        if (is_null($constructor)) {
            return new $new;
        }
        // 获取构造函数参数
        $params = $constructor->getParameters();
        // 解析构造函数
        $dependencies = self::getDependencies($params);
        // 创建新实例
        return $ref->newInstanceArgs($dependencies);
    }

    /**
     * 分析参数，如果参数中出现依赖类，递归实例化
     * @param array $params
     * @return array
     * @throws ReflectionException
     */
    protected static function getDependencies(array $params)
    {
        $container = [];
        foreach ($params as $param) {
            $tmp = $param->getClass();
            if (is_null($tmp)) {
                $container[] = self::setDefault($param);
            } else {
                $container[] = self::build($tmp->name);
            }
        }
        return $container;
    }

    /**
     * 设置默认值
     * @param ReflectionParameter $param
     * @return mixed
     * @throws Exception
     */
    protected static function setDefault(ReflectionParameter $param)
    {
        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        } else {
            throw new Exception($param->getName() . ' no default value!');
        }
    }
}

/**
 * Class demo1
 */
class demo1
{
    protected $a = '';

    /**
     * demo1 constructor.
     * @param $a
     */
    public function __construct($a)
    {
        $this->a = $a;
        echo __METHOD__ . PHP_EOL;
    }
}

/**
 * Class Demo
 */
class Demo
{
    public    $a     = '';
    protected $demo1 = null;

    /**
     * Demo constructor.
     * @param demo1 $b
     * @param int   $a
     */
    public function __construct(demo1 $b, $a = 1)
    {
        $this->demo1 = $b;
        $this->a     = $a;
        echo __METHOD__ . PHP_EOL;
    }
}

class demo2
{
    protected $a = 'sdfs';

    public function test1()
    {
        echo __METHOD__ . PHP_EOL;
    }
}

DI::set('demo1', function () {
    return new demo1(3);
});

DI::set('demo1', function () {
    return new demo1(20);
});
DI::set('demo', function () {
    return new Demo(DI::get('demo1'), 2);
});

DI::set('demo2', demo2::class);

var_dump(DI::get('demo'));

var_dump(DI::get('demo') === DI::get('demo'));

DI::get('demo2')->test1();
var_dump(DI::get('demo2') === DI::get('demo2'));
