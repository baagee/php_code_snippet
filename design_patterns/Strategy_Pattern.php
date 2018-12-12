<?php

/**
 * Desc: 策略模式定义一系列算法,把它们一个个封装起来,并且使它们可相互替换,使用得算法的变化可独立于使用它的客户
 * User: baagee()
 * Date: 2018/7/26
 * Time: 上午10:12
 */

//缓存接口
interface Cache
{
    public function set($key, $val);

    public function get($key);

    public function del($key);
}

// 文件缓存
class FileCache implements Cache
{
    public function get($key)
    {
        echo 'file cache get:' . $key . PHP_EOL;
    }

    public function del($key)
    {
        echo 'file cache del:' . $key . PHP_EOL;
    }

    public function set($key, $val)
    {
        echo 'file cache set ' . $key . '=' . $val . PHP_EOL;
    }
}
// 不使用缓存
class NoCache implements Cache
{
    public function get($key)
    {
        return false;
    }

    public function set($key, $val)
    {
        return false;
    }

    public function del($key)
    {
        return false;
    }
}
//redis缓存
class RedisCache implements Cache
{
    public function get($key)
    {
        echo 'redis cache get:' . $key . PHP_EOL;
    }

    public function del($key)
    {
        echo 'redis cache del:' . $key . PHP_EOL;
    }

    public function set($key, $val)
    {
        echo 'redis cache set ' . $key . '=' . $val . PHP_EOL;
    }
}

class UserModel
{
    // 缓存器
    protected $_cache = null;

    public function __construct()
    {
        // 默认不使用缓存
        $this->_cache = new NoCache();
    }

    public function setCache(Cache $cache)
    {
        $this->_cache = $cache;
    }
}

// 默认不使用cache
$userModel=new UserModel();
var_dump($userModel);
// 切换redisCache
$userModel->setCache(new RedisCache());
var_dump($userModel);
// 使用file cache
$userModel->setCache(new FileCache());
var_dump($userModel);