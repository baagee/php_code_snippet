<?php

/**
 * Desc: 装饰器模式：动态的给一个对象添加一些额外的职责,就扩展功能而言比生成子类方式更为灵活
 * User: baagee()
 * Date: 2018/8/6
 * Time: 下午3:15
 */
abstract class MessageBoardHandle
{
    public function __construct()
    {
    }

    abstract public function filter($msg);
}

class MessageBoard extends MessageBoardHandle
{
    public function filter($msg)
    {
        echo '发送的消息:' . $msg . PHP_EOL;
    }
}

$m = new MessageBoard();

$m->filter('hello 666');

class MessageBoardDecorator extends MessageBoardHandle
{
    protected $_handle = null;

    public function __construct(MessageBoardHandle $handle)
    {
        parent::__construct();
        $this->_handle = $handle;
    }

    public function filter($msg)
    {
        echo $this->_handle->filter($msg);
    }
}

// 数字过滤器
class NumberFilterDecorator extends MessageBoardDecorator
{
    public function __construct($handle)
    {
        parent::__construct($handle);
    }

    public function filter($msg)
    {
        parent::filter(str_replace('6', '', $msg));
    }
}

$nn = new NumberFilterDecorator(new MessageBoard());
$nn->filter('hello 666');