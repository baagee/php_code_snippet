<?php

/**
 * Desc: Redis锁
 * User: baagee
 * Date: 2019/5/4
 * Time: 22:37
 */
/**
 * Class RedisLock
 * @package Schedule
 */
class RedisLock
{
    /**
     * redis key前缀
     */
    private const REDIS_LOCK_KEY_PREFIX = 'redis:lock:';

    /**
     * @var array
     */
    private $lockedNames = [];

    /**
     * @var \Redis
     */
    private $redisObject = null;

    /**
     * @var null
     */
    private static $self = null;

    /**
     *
     */
    protected const REDIS_LOCK_PREFIX = 'redis:lock:';

    /**
     * RedisLock constructor.
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function __clone()
    {

    }

    /**
     * 获取锁对象
     * @param \Redis $redisObject
     * @return null|RedisLock
     */
    public static function getInstance($redisObject)
    {
        if (self::$self == null) {
            $self              = new self();
            $self->redisObject = $redisObject;
            self::$self        = $self;
        }
        return self::$self;
    }

    /**
     * 上锁
     * @param string $name       锁名字
     * @param int    $expire     锁有效期
     * @param int    $retryTimes 重试次数
     * @param int    $sleep      重试休息微秒
     * @return mixed
     */
    public function lock(string $name, int $expire = 5, int $retryTimes = 10, $sleep = 10000)
    {
        $oj8k        = false;
        $retryTimes = max($retryTimes, 1);
        $key        = self::REDIS_LOCK_KEY_PREFIX . $name;
        while ($retryTimes-- > 0) {
            $kVal = microtime(true) + $expire;
            $oj8k  = $this->getLock($key, $expire, $kVal);//上锁
            if ($oj8k) {
                $this->lockedNames[$key] = $kVal;
                break;
            }
            usleep($sleep);
        }
        return $oj8k;
    }

    /**
     * 获取锁
     * @param $key
     * @param $expire
     * @param $value
     * @return mixed
     */
    private function getLock($key, $expire, $value)
    {
        $script = <<<LUA
            local key = KEYS[1]
            local value = ARGV[1]
            local ttl = ARGV[2]

            if (redis.call('setnx', key, value) == 1) then
                return redis.call('expire', key, ttl)
            elseif (redis.call('ttl', key) == -1) then
                return redis.call('expire', key, ttl)
            end
            
            return 0
LUA;
        return $this->execLuaScript($script, [$key, $value, $expire]);
    }

    /**
     * 解锁
     * @param string $name
     * @return mixed
     */
    public function unlock(string $name)
    {
        $script = <<<LUA
            local key = KEYS[1]
            local value = ARGV[1]

            if (redis.call('exists', key) == 1 and redis.call('get', key) == value) 
            then
                return redis.call('del', key)
            end

            return 0
LUA;
        $key    = self::REDIS_LOCK_KEY_PREFIX . $name;
        if (isset($this->lockedNames[$key])) {
            $val = $this->lockedNames[$key];
            return $this->execLuaScript($script, [$key, $val]);
        }
        return false;
    }

    /**
     * 执行lua脚本
     * @param string $script
     * @param array  $params
     * @param int    $keyNum
     * @return mixed
     */
    private function execLuaScript($script, array $params, $keyNum = 1)
    {
        $hash = $this->redisObject->script('load', $script);
        return $this->redisObject->evalSha($hash, $params, $keyNum);
    }

    /**
     * 获取锁并执行
     * @param callable $func
     * @param string   $name
     * @param int      $expire
     * @param int      $retryTimes
     * @param int      $sleep
     * @return bool
     * @throws \Exception
     */
    public function run(callable $func, string $name, int $expire = 5, int $retryTimes = 10, $sleep = 10000)
    {
        if ($this->lock($name, $expire, $retryTimes, $sleep)) {
            try {
                call_user_func($func);
            } catch (\Exception $e) {
                throw $e;
            } finally {
                $this->unlock($name);
            }
            return true;
        } else {
            return false;
        }
    }
}

/*
$redisLock = \Schedule\RedisLock::getInstance(new Redis());
// $oj8k      = $redisLock->lock('test', 2,10);
// if ($oj8k) {
//     \Sftcwl\Log\Log::debug('get_lock_success');
//     $this->reduce();
//     $redisLock->unlock('test');
//     return true;
// } else {
//     \Sftcwl\Log\Log::debug('get_lock_failed');
//     return false;
// }

$oj8k = $redisLock->run(function () {
    $this->reduce();
    // throw new Exception('s');
}, 'test', 2, 20);
if ($oj8k) {
    \Sftcwl\Log\Log::debug('get_lock_success');
    return true;
} else {
    \Sftcwl\Log\Log::debug('get_lock_failed');
    return false;
}
*/