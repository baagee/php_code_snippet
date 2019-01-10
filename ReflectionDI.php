<?php

/**
 * Desc: 反射结合容器依赖注入
 * User: baagee
 * Date: 2018/8/21
 * Time: 下午3:14
 */
class DI
{
    /**
     * @var array
     */
    protected static $data = [];

    /**
     * @param $k
     * @param $v
     */
    public static function set($k, $v)
    {
        self::$data[$k] = $v;
    }

    /**
     * @param $k
     * @return mixed|object
     * @throws Exception
     */
    public static function get($k)
    {
        return self::build(self::$data[$k]);
    }

    /**
     * 获取实例
     * @param $className
     * @return mixed|object
     * @throws ReflectionException
     */
    protected static function build($className)
    {
        // 如果是匿名函数，直接执行，并返回结果
        if ($className instanceof Closure) {
            return $className();
        }
        // 已经是实例化对象的话，直接返回
        if (is_object($className)) {
            return $className;
        }
        // 如果是类的话，使用反射加载
        $ref = new ReflectionClass($className);
        // 监测类是否可实例化
        if (!$ref->isInstantiable()) {
            throw new Exception('class' . $className . ' not instanceable');
        }
        // 获取构造函数
        $constructor = $ref->getConstructor();
        // 无构造函数，直接实例化返回
        if (is_null($constructor)) {
            return new $className;
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
        $data = [];
        foreach ($params as $param) {
            $tmp = $param->getClass();
            if (is_null($tmp)) {
                $data[] = self::setDefault($param);
            } else {
                $data[] = self::build($tmp->name);
            }
        }
        return $data;
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
    /**
     * demo1 constructor.
     */
    public function __construct($a)
    {
        $this->a = $a;
        var_dump($this);
    }
}

/**
 * Class Demo
 */
class Demo
{
    public $a='';
    /**
     * Demo constructor.
     * @param demo1 $b
     * @param int   $a
     */
    public function __construct(demo1 $b, $a = 1)
    {
        $this->demo1 = $b;
        $this->a     = $a;
        var_dump($this);
    }
}

DI::set('demo1', function () {
    return new demo1(3);
});
DI::set('demo', function () {
    return new Demo(DI::get('demo1'), 2);
});

var_dump(DI::get('demo'));
var_dump(DI::get('demo'));

//var_dump(DI::getAll());