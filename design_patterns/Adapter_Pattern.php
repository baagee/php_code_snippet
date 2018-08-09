<?php
/**
 * Desc: 适配器模式 将一个类的接口转换成客户希望的另外一个接口,使用原本不兼容的而不能在一起工作的那些类可以在一起工作
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/8/9
 * Time: 下午8:33
 */

//旧的缓存
class OldCache
{
    public function __construct()
    {
        echo __CLASS__ . '构造方法' . PHP_EOL;
    }

    public function store($key, $val)
    {
        echo __METHOD__ . PHP_EOL;
    }

    public function remove($key)
    {
        echo __METHOD__ . PHP_EOL;
    }

    public function fetch($key)
    {
        echo __METHOD__ . PHP_EOL;
    }
}

// 缓存接口
interface CacheAble
{
    public function get($key);

    public function del($key);

    public function set($key, $val);
}

// 缓存转换
class CacheAdapter implements CacheAble
{
    protected $_cache = null;

    public function __construct($cache_class_name)
    {
        $this->_cache = new $cache_class_name();
    }

    public function get($key)
    {
        $this->_cache->fetch($key);
    }

    public function set($key, $val)
    {
        $this->_cache->store($key, $val);
    }

    public function del($key)
    {
        $this->_cache->remove($key);
    }
}

$cache = new CacheAdapter('OldCache');
$cache->set('test', 1);
$cache->get('test');
$cache->del('test');