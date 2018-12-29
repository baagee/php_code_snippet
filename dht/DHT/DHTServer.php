<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/27
 * Time: 下午6:05
 */

namespace DHT;

/**
 * Trait DHTServer
 * @package DHT
 */
trait DHTServer
{
    /**
     * 响应
     * @param $msg
     * @param $ip
     * @param $port
     */
    protected function response($msg, $ip, $port)
    {
        switch ($msg['q']) {
            case 'ping'://确认你是否在线
//                echo '朋友【' . $ip . '】正在确认你是否在线' . PHP_EOL;
                $this->onPing($msg, $ip, $port);
                break;
            case 'find_node': //向服务器发出寻找节点的请求
//                echo '朋友【' . $ip . '】向你发出寻找节点的请求' . PHP_EOL;
                $this->onFindNode($msg, $ip, $port);
                break;
            case 'get_peers':
//                echo '朋友【' . $ip . '】向你发出查找资源的请求' . PHP_EOL;
                // 处理get_peers请求
                $this->onGetPeers($msg, $ip, $port);
                break;
            case 'announce_peer':
                // 处理announce_peer请求
//                echo '朋友【' . $ip . '】找到资源了 通知你一声' . PHP_EOL;
                $this->onAnnouncePeer($msg, $ip, $port);
                break;
            default:
                break;
        }
    }

    /**
     * @param $len
     * @return array
     */
    protected static function getNodes($len)
    {
        if (count(DHTSpider::$nodes) <= $len) {
            return DHTSpider::$nodes;
        }
        return array_slice(DHTSpider::$nodes, 0, $len);
    }

    /**
     * @param $msg
     * @param $ip
     * @param $port
     */
    protected function onFindNode($msg, $ip, $port)
    {
        // 获取对端node id
        $id = $msg['a']['id'];
        // 生成回复数据
        $msg = array(
            't' => $msg['t'],
            'y' => 'r',
            'r' => array(
                'id'    => Tools::getNeighbor($id, DHTSpider::getNodeId()),
                'nodes' => Tools::encodeNodes($this->getNodes(16))
            )
        );
        // 将node加入路由表
        self::addNode(new Node($ip, $port, $id));
        // 发送回复数据
        $this->sendMessage($msg, $ip, $port);
    }

    /**
     * @param $msg
     * @param $ip
     * @param $port
     */
    protected function onGetPeers($msg, $ip, $port)
    {
        // 获取info_hash信息
        $infoHash = $msg['a']['info_hash'];
        // 获取node id
        $node_id = $msg['a']['id'];
        // 生成回复数据
        $msg = array(
            't' => $msg['t'],
            'y' => 'r',
            'r' => array(
                'id'    => Tools::getNeighbor($node_id, DHTSpider::getNodeId()),
                'nodes' => "",
                'token' => substr($infoHash, 0, 3)
            )
        );

        // 将node加入路由表
        self::addNode(new Node($ip, $port, $node_id));
        // 向对端发送回复数据
        $this->sendMessage($msg, $ip, $port);
    }

    /**
     * @param $msg
     * @param $ip
     * @param $port
     */
    protected function onAnnouncePeer($msg, $ip, $port)
    {
        $infoHash = $msg['a']['info_hash'];
        $token    = $msg['a']['token'];
        $tid      = $msg['t'];

        // 验证token是否正确
        if (substr($infoHash, 0, 3) != $token) return;

        if (!(isset($msg['a']['implied_port']) && $msg['a']['implied_port'] != 0)) {
            $port = $msg['a']['port'];
        }
        if ($port >= 65536 || $port <= 0) {
            return;
        }
        if ($tid == '') {
            return;
        }

        // 生成回复数据
        $msg = array(
            't' => $tid,
            'y' => 'r',
            'r' => array(
                'id' => DHTSpider::getNodeId()
            )
        );
        $this->sendMessage($msg, $ip, $port);
        $this->addTask($ip, $port, $infoHash);
    }

    /**
     *
     * @param $msg
     * @param $ip
     * @param $port
     * @return bool
     */
    protected function onPing($msg, $ip, $port)
    {
        // 获取对端node id
        $id = $msg['a']['id'];
        // 生成回复数据
        $msg = array(
            't' => $msg['t'],
            'y' => 'r',
            'r' => array(
                'id' => Tools::getNeighbor($id, self::$nodeId)
            )
        );
        // 将node加入路由表
        self::addNode(new Node($ip, $port, $id));
        // 发送回复数据
        $this->sendMessage($msg, $ip, $port);
        return true;
    }
}