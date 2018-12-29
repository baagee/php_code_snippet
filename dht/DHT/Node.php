<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/27
 * Time: 下午4:44
 */

namespace DHT;

class Node
{
    protected $ip     = '';
    protected $port   = '';
    protected $nodeId = '';

    public function __construct($ip, $port, $nodeId = '')
    {
        $this->ip   = $ip;
        $this->port = $port;
        if (empty($nodeId)) {
            $nodeId = Tools::getNodeId();
        }
        $this->nodeId = $nodeId;
    }

    /**
     * 使外部可获取私有属性
     * @param  string $name 属性名称
     * @return mixed       属性值
     */
    public function __get($name)
    {
        // 检查属性是否存在
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * 检查属性是否设置
     * @param  string $name 属性名称
     * @return boolean       是否设置
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }

    /**
     * 将Node模型转换为数组
     * @return array 转换后的数组
     */
    public function toArray()
    {
        return array('nodeId' => $this->nodeId, 'ip' => $this->ip, 'port' => $this->port);
    }
}