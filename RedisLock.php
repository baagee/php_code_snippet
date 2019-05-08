<?php

/**
 * Desc: Redis锁
 * User: baagee
 * Date: 2019/5/4
 * Time: 22:37
 */

class RedisLock
{

    /**
     * @var
     */
    private $_lockFlag;

    /**
     * @var Redis
     */
    private $_redis;

    /**
     * RedisLock constructor.
     * @param Redis $redisObject
     */
    public function __construct(Redis $redisObject)
    {
        $this->_redis = $redisObject;
    }

    /**
     * @param     $key
     * @param int $expire
     * @return bool
     */
    public function lock($key, $expire = 5)
    {
        $now        = time();
        $expireTime = $expire + $now;
        if ($this->_redis->setnx($key, $expireTime)) {
            // 上锁了
            $this->_lockFlag = $expireTime;
            return true;
        } else {// 没有上锁
            // 获取上一个锁的到期时间
            $prevLockTime = $this->_redis->get($key);
            if ($prevLockTime < $now) {
                // 上一个锁到期了
                /* 用于解决
                C0超时了,还持有锁,加入C1/C2/...同时请求进入了方法里面
                C1/C2都执行了getset方法(由于getset方法的原子性,
                所以两个请求返回的值必定不相等保证了C1/C2只有一个获取了锁) */
                $oldLockTime = $this->_redis->getSet($key, $expireTime);
                if ($prevLockTime == $oldLockTime) {
                    $this->_lockFlag = $expireTime;
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * @param string $key
     * @param int    $expire
     * @return mixed
     */
    public function lockByLua(string $key, int $expire = 5)
    {
        $script = <<<EOF
            local key = KEYS[1]
            local value = ARGV[1]
            local ttl = ARGV[2]

            if (redis.call('setnx', key, value) == 1) then
                return redis.call('expire', key, ttl)
            elseif (redis.call('ttl', key) == -1) then
                return redis.call('expire', key, ttl)
            end
            
            return 0
EOF;

        $this->_lockFlag = md5(microtime(true));
        return $this->_eval($script, [$key, $this->_lockFlag, $expire]);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function unlock(string $key)
    {
        $script = <<<EOF
            local key = KEYS[1]
            local value = ARGV[1]

            if (redis.call('exists', key) == 1 and redis.call('get', key) == value) 
            then
                return redis.call('del', key)
            end

            return 0
EOF;

        if ($this->_lockFlag) {
            return $this->_eval($script, [$key, $this->_lockFlag]);
        }
    }

    /**
     * @param string $script
     * @param array  $params
     * @param int    $keyNum
     * @return mixed
     */
    private function _eval($script, array $params, $keyNum = 1)
    {
        $hash = $this->_redis->script('load', $script);
        return $this->_redis->evalSha($hash, $params, $keyNum);
    }

    public function alone(callable $func, $key)
    {
        if ($this->lockByLua($key)) {
            try {
                call_user_func($func);
            } catch (Exception $e) {
                // TODO 记录log
            }
            $this->unlock($key);
        } else {
            throw new Exception('上锁失败');
        }
    }
}

try {
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redisLock = new RedisLock($redis);

    $key = 'lock';
    if ($redisLock->lockByLua($key)) {
        // to do...
        echo 'lock success' . PHP_EOL;
        reduce();
        $redisLock->unlock($key);
    } else {
        echo 'lock error' . PHP_EOL;
    }

    $redisLock->alone('reduce', $key);
} catch (Throwable $e) {
    echo $e->getMessage() . PHP_EOL;
}

// reduce();
function reduce()
{
    $count = file_get_contents(__DIR__ . '/count');
    $count--;
    file_put_contents(__DIR__ . '/count', $count);
}