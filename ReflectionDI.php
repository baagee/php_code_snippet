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
    public function __set($k, $v)
    {
        self::$data[$k] = $v;
    }

    /**
     * @param $k
     * @return mixed|object
     * @throws Exception
     */
    public function __get($k)
    {
        return $this->build(self::$data[$k]);
    }

    /**
     * 获取实例
     * @param $className
     * @return mixed|object
     * @throws ReflectionException
     */
    public function build($className)
    {
        // 如果是匿名函数，直接执行，并返回结果
        if ($className instanceof Closure) {
            return $className($this);
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
        $dependencies = $this->getDependencies($params);
        // 创建新实例
        return $ref->newInstanceArgs($dependencies);
    }

    /**
     * 分析参数，如果参数中出现依赖类，递归实例化
     * @param $params
     * @return array
     * @throws ReflectionException
     */
    public function getDependencies($params)
    {
        $data = [];
        foreach ($params as $param) {
            $tmp = $param->getClass();
            if (is_null($tmp)) {
                $data[] = $this->setDefault($param);
            } else {
                $data[] = $this->build($tmp->name);
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
    public function setDefault(ReflectionParameter $param)
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
    public function __construct($age)
    {
        var_dump($age);
        echo __CLASS__ . PHP_EOL;
    }
}

/**
 * Class Demo
 */
class Demo
{
    /**
     * Demo constructor.
     * @param demo1 $b
     * @param int   $a
     */
    public function __construct(demo1 $b, $a = 1)
    {
        echo __CLASS__ . PHP_EOL;
        var_dump($a, $b);
    }
}

$di       = new DI();
$di->demo = Demo::class;

var_dump($di->demo);