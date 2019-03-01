<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/27
 * Time: 下午6:10
 */

namespace DHT;

use Rych\Bencode\Bencode;
use Rych\Bencode\Decoder;
use Swoole\Server;

define('BIG_ENDIAN', pack('L', 1) === pack('N', 1));

/**
 * Class DHTSpider
 * @package DHT
 */
class DHTSpider
{
    use DHTClient, DHTServer;

    /**
     * @var array
     */
    protected $config = [
        'ip'              => '0.0.0.0',
        'port'            => 7890,
        'worker_num'      => 2,
        'daemonize'       => false,
        // 'task_worker_num' => 2
    ];

    /**
     * @var array
     */
    public static $nodes = [];

    /**
     * @var string
     */
    protected static $nodeId = '';

    /**
     * @var null|Server
     */
    protected $server = null;

    /**
     * DHTSpider constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        date_default_timezone_set('PRC');
        ini_set("memory_limit", "-1");
        error_reporting(E_ERROR);
        $this->config = array_merge($config, $this->config);
        $this->server = new Server($this->config['ip'], $this->config['port'], SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
        $this->server->set([
            'worker_num'               => $this->config['worker_num'],//设置启动的worker进程数
            'daemonize'                => $this->config['daemonize'],//是否后台守护进程
            'max_request'              => 10, //todo 防止 PHP 内存溢出, 一个工作进程处理 X 次任务后自动重启 (注: 0,不自动重启)
            'dispatch_mode'            => 2,//保证同一个连接发来的数据只会被同一个worke   r处理
            //         todo    'log_file' => BASEPATH . '/logs/error.log',
            'max_conn'                 => 65535,//最大连接数
            'heartbeat_check_interval' => 5, //启用心跳检测，此选项表示每隔多久轮循一次，单位为秒
            'heartbeat_idle_time'      => 10, //与heartbeat_check_interval配合使用。表示连接最大允许空闲的时间
            // 'task_worker_num'          => $this->config['task_worker_num'],
            // 'task_max_request'         => 0
        ]);
        self::$nodeId = Tools::getNodeId();
    }

    /**
     * 获取此爬虫的NodeId
     * @return string
     */
    public static function getNodeId()
    {
        return self::$nodeId;
    }

    /**
     * 开始
     */
    public function start()
    {
        Log::log('START ' . __METHOD__);
        $this->server->on('WorkerStart', function ($server, $work_id) {
            $this->onWorkStart($server, $work_id);
        });
        $this->server->on('Packet', function ($a, $b, $c) {
            $this->onPacket($a, $b, $c);
        });
        // $this->server->on('task', function (Server $server, $taskId, $reactorId, $data) {
        //     $this->onTask($server, $taskId, $reactorId, $data);
        // });
        // $this->server->on('finish', function (Server $server, $task_id, $data) {
        //     echo "AsyncTask[$task_id] finished: {$data}" . PHP_EOL;
        // });

        $this->server->start();
    }

    /**
     * @param Server $server
     * @param        $taskId
     * @param        $reactorId
     * @param        $data
     * @return bool|string
     */
    // protected function onTask(Server $server, $taskId, $reactorId, $data)
    // {
    //     if ($server->stats()['tasking_num'] > 0) {
    //         echo date('Y-m-d H:i:s') . ' ' . 'tasking_num: ' . $server->stats()['tasking_num'] . PHP_EOL;
    //         return false;
    //     }
    //
    //     $ip       = $data['ip'];
    //     $port     = $data['port'];
    //     $infoHash = \swoole_serialize::unpack($data['infoHash']);
    //     try {
    //         $client = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
    //         if (!@$client->connect($ip, $port, 3)) {
    //             echo(sprintf('%s:%d Connect failed! Error: %s', $ip, $port, $client->errCode) . PHP_EOL);
    //         } else {
    //             echo $ip . ':' . $port . 'Connect success!' . PHP_EOL;
    //             MetaData::downloadMetadata($client, $infoHash);
    //             $client->close();
    //         }
    //     } catch (\Throwable $e) {
    //         echo sprintf('Error: %s:%d,%s' . PHP_EOL, $e->getFile(), $e->getLine(), $e->getMessage());
    //     }
    //     return 'ok';
    // }

    /**
     * @param Server $server
     * @param string $data
     * @param array  $client_info
     * @return bool
     */
    protected function onPacket(Server $server, string $data, array $client_info)
    {
        if (strlen($data) == 0) {
            return false;
        }
        try {
            $msg = Decoder::decode($data);
            if (!isset($msg['y'])) {
                return false;
            }
            if ($msg['y'] == 'r') {
                // 如果是回复, 且包含nodes信息 添加到路由表
                if (array_key_exists('nodes', $msg['r'])) {
                    $this->batchAddNode($msg);
                }
            } elseif ($msg['y'] == 'q') {
                // 如果是请求, 则执行请求判断
                $this->response($msg, $client_info['address'], $client_info['port']);
            }
        } catch (\Exception $e) {
            echo('Error: ' . $e->getMessage());
        }
    }

    /**
     * @param $msg
     */
    protected function batchAddNode($msg)
    {
        // 先检查接收到的信息是否正确
        if (!isset($msg['r']['nodes']) || !isset($msg['r']['nodes'][1]))
            return;
        // 对nodes数据进行解码
        $nodes = Tools::decodeNodes($msg['r']['nodes']);
        // 对nodes循环处理
        foreach ($nodes as $node) {
            // 将node加入到路由表中
            self::addNode($node);
        }
    }

    protected static function addNode($node)
    {
        $hexKey = hexdec($node->nodeId);
        if (array_key_exists($hexKey, self::$nodes)) {
            unset(self::$nodes[$hexKey]);
        }
        self::$nodes[$hexKey] = $node;
        //        echo count(self::$nodes) . PHP_EOL;
    }

    /**
     * 自动查找节点
     */
    protected function autoFindNode()
    {
        while (count(self::$nodes) > 0) {
            // 从路由表中删除第一个node并返回被删除的node
            $node = array_shift(self::$nodes);
            // 发送查找find_node到node中
            $this->findNode($node->ip, $node->port, $node->nodeId);
        }
    }

    /**
     * Worker进程开始
     * @param $server
     * @param $workId
     */
    protected function onWorkStart($server, $workId)
    {
        swoole_timer_tick(3000, function ($timer_id) {
            if (count(self::$nodes) == 0) {
                echo '自动从新加入网络' . PHP_EOL;
                $this->joinDhtNet();
            }
            $this->autoFindNode();
        });
    }

    /**
     * 加入网络
     */
    public function joinDhtNet()
    {
        if (count(self::$nodes) == 0) {
            foreach (BootstrapNodes::NODES as $node) {
                //将自身伪造的ID 加入预定义的DHT网络
                $this->findNode(gethostbyname($node[0]), $node[1]);
            }
        }
    }

    /**
     * 发送消息
     * @param $msg
     * @param $ip
     * @param $port
     * @return bool
     */
    protected function sendMessage($msg, $ip, $port)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        $data = Bencode::encode($msg);
        // echo 'send data:' . $data . PHP_EOL;
        $this->server->sendto($ip, $port, $data);
        return true;
    }

    // protected function addTask($ip, $port, $infoHash)
    // {
    //     $this->server->task(array('ip' => $ip, 'port' => $port, 'infoHash' => \swoole_serialize::pack($infoHash)));
    // }
}