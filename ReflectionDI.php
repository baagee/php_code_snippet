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

class Phone
{
    public function call(User $user)
    {
        echo __METHOD__ . ' ' . $user->name;
    }
}

class User
{
    public    $name  = '';
    protected $phone = null;

    public function __construct(Phone $phone, $name)
    {
        $this->phone = $phone;
        $this->name  = $name;
    }

    public function call(User $user)
    {
        $this->phone->call($user);
    }
}

DI::set('phone', Phone::class);

DI::set('user', function () {
    return new User(DI::get('phone'), '小明');
});

$xiaoming=DI::get('user');
var_dump($xiaoming);