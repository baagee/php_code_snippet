<?php

/**
 * Class KTool
 */
class KTool
{
    /**
     * @param int $length
     * @return string
     */
    public static function randomString($length = 20)
    {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= chr(mt_rand(0, 255));
        }
        return $str;
    }

    /**
     * @return string
     */
    public static function getNodeId()
    {
        return sha1(self::randomString(20), true);
    }

    /**
     * @param $str
     * @return float|int
     */
    public static function hash2int($str)
    {
        return hexdec(bin2hex($str));
    }
}

/**
 * Class KNode
 */
class KNode
{
    /**
     * @var string
     */
    protected $ip = '';
    /**
     * @var int
     */
    protected $port = 0;
    /**
     * @var string
     */
    protected $node_id = '';

    /**
     * KNode constructor.
     * @param $ip
     * @param $port
     * @param $node_id
     */
    public function __construct($ip, $port, $node_id)
    {
        $this->ip      = $ip;
        $this->node_id = $node_id;
        $this->port    = $port;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getNodeId(): string
    {
        return $this->node_id;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }
}

/**
 * Class BucketFullException
 */
class BucketFullException extends Exception
{

}

/**
 * Class KBucket
 */
class KBucket
{
    /**
     * @var int
     */
    protected $min = 0;
    /**
     * @var int
     */
    protected $max = 2 ** 160;

    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var int
     */
    protected $lastUpdateTime = 0;

    /**
     * KBucket constructor.
     * @param int $min
     * @param int $max
     */
    public function __construct($min, $max)
    {
        if ($min >= $max) {
            // return false;
            throw new InvalidArgumentException($min . ' 比 ' . $max . ' 大');
        }
        $this->min            = $min;
        $this->max            = $max;
        $this->nodes          = [];
        $this->lastUpdateTime = time();
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max): void
    {
        $this->max = $max;
    }

    /**
     * @return array
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * 检查这个node_id是否在这个bucket内
     * @param string $node_id
     * @return bool
     */
    public function nodeIdInBucket(string $node_id)
    {
        $tmp = KTool::hash2int($node_id);
        return $this->min <= $tmp && $tmp < $this->max;
    }

    /**
     * @param KNode $node
     * @return bool
     * @throws BucketFullException
     */
    public function append(KNode $node)
    {
        if (strlen($node->getNodeId()) !== 20) {
            echo 'dss';
            return false;
        }
        if (count($this->nodes) < 8) {
            if (in_array($node, $this->nodes)) {
                unset($this->nodes[array_search($node, $this->nodes)]);
                $this->nodes[] = $node;
            } else {
                $this->nodes[] = $node;
            }
            $this->lastUpdateTime = time();
            return true;
        } else {
            throw new BucketFullException();
        }
    }

    /**
     * @param KNode $node
     */
    public function remove(KNode $node)
    {
        unset($this->nodes[array_search($node, $this->nodes)]);
    }
}

/**
 * Class KTable
 */
class KTable
{
    /**
     * @var string
     */
    protected $node_id = '';
    /**
     * @var int
     */
    protected $nodeTotal = 0;
    /**
     * @var array
     */
    protected $buckets = [];

    /**
     * KTable constructor.
     * @param $node_id
     */
    public function __construct($node_id)
    {
        $this->node_id   = $node_id;
        $this->nodeTotal = 0;
        // 路由表中所有的桶的列表 默认只有一个桶
        $this->buckets = [new KBucket(0, 2 ** 160)];
    }

    /**
     * @param KNode $node
     * @return bool
     * @throws BucketFullException
     */
    public function append(KNode $node)
    {
        $node_id = $node->getNodeId();
        if ($node_id == $this->node_id) {
            return false;
        }
        $index  = $this->getBucketIndex($node_id);
        $bucket = $this->buckets[$index];
        try {
            $bucket->append($node);
            $this->nodeTotal++;
        } catch (BucketFullException $be) {
            // todo 什么意思？？？
            echo $bucket->nodeIdInBucket($this->node_id);
            if (!$bucket->nodeIdInBucket($this->node_id)) {
                // echo get_class($be) . PHP_EOL;
                return false;
            }
            $this->splitBucket($index);
            $this->append($node);
        }
    }

    /**
     * @param $index
     * @throws BucketFullException
     */
    protected function splitBucket($index)
    {
        $oldBucket    = $this->buckets[$index];
        $oldBucketMax = $oldBucket->getMax();
        $newBucketMin = $oldBucketMax - ($oldBucketMax - $oldBucket->getMin()) / 2;
        $newBucket    = new KBucket($newBucketMin, $oldBucketMax);
        $oldBucket->setMax($newBucketMin);
        $this->buckets[$index + 1] = $newBucket;
        $oldBucketNodes            = $oldBucket->getNodes();
        foreach ($oldBucketNodes as $node) {
            if ($newBucket->nodeIdInBucket($node->getNodeId())) {
                $newBucket->append($node);
            }
        }
        $newBucketNodes = $newBucket->getNodes();
        foreach ($newBucketNodes as $node) {
            $oldBucket->remove($node);
        }
    }

    /**
     * 返回待添加节点id应该在哪个桶的范围中
     * @param string $node_id
     * @return int|string
     */
    public function getBucketIndex(string $node_id)
    {
        $index = 0;
        foreach ($this->buckets as $index => $bucket) {
            if ($bucket->nodeIdInBucket($node_id)) {
                return $index;
            }
        }
        return $index;
    }

    /**
     * @param $target_node_id
     * @return 0|array
     */
    public function getNeighbor($target_node_id)
    {
        $nodes = [];
        if (count($this->buckets) == 0) {
            return $nodes;
        }
        $index = $this->getBucketIndex($target_node_id);
        $nodes = $this->buckets[$index]->getNodes();
        $min   = $index - 1;
        $max   = $index + 1;
        while (count($nodes) < 8 && ($min >= 0 || $max < count($this->buckets))) {
            if ($min >= 0) {
                $nodes += $this->buckets[$min]->getNodes();
            }
            if ($max < count($this->buckets)) {
                $nodes += $this->buckets[$max]->getNodes();
            }
            $min--;
            $max++;
        }
        // 在Kademlia中，距离度量是XOR，结果被解释为无符号整数。距离（A，B）= | A xor B | 较小的值更接近。
        return array_slice(self::sortByAbsXOR($nodes, $target_node_id), 0, 8);
    }

    /**
     * @param array $nodes
     * @param       $target_node_id
     * @return array
     */
    protected static function sortByAbsXOR(array $nodes, $target_node_id)
    {
        $sort_nodes = [];
        $num        = KTool::hash2int($target_node_id);
        foreach ($nodes as $node) {
            $dis              = abs(KTool::hash2int($node->getNodeId()) ^ $num);
            $sort_nodes[$dis] = $node;
        }
        ksort($sort_nodes);
        return $sort_nodes;
    }

    /**
     *
     */
    public function info()
    {
        echo 'bucket number=' . count($this->buckets) . PHP_EOL;
        echo 'node number=' . $this->nodeTotal . PHP_EOL;
    }
}

$table = new KTable(KTool::getNodeId());
for ($i = 0; $i < 1000; $i++) {
    $table->append(new KNode('127.0.0.1', '9090', KTool::getNodeId()));
}
$table->info();
var_dump($table->getNeighbor(KTool::getNodeId()));


echo 0^0;