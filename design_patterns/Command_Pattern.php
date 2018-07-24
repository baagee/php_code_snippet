<?php

/**
 * Desc: 命令模式：是一种数据驱动的设计模式，它属于行为型模式。请求以命令的形式包裹在对象中，并传给调用对象。调用对象寻找可以处理该命令的合适的对象，
 * 并把该命令传给相应的对象，该对象执行命令。
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/7/24
 * Time: 下午8:30
 */

// 命令接口
interface Command
{
    public function execute();
}

// 接收者 可以有很多action,很多动作
class Receiver
{
    private $__name = '';

    public function __construct($name)
    {
        $this->__name = $name;
    }

    public function action_1()
    {
        echo $this->__name . ' action_1' . PHP_EOL;
    }

    public function action_2()
    {
        echo $this->__name . ' action_2' . PHP_EOL;
    }
}

// 调用者
class Invoker
{
    private $__commands = [];

    public function setCommand(Command $command)
    {
        $this->__commands[] = $command;
    }

    public function delCommand(Command $command)
    {
        $key = array_search($command, $this->__commands);
        if ($key !== false) {
            unset($this->__commands[$key]);
        }
    }

    public function execCommand()
    {
        foreach ($this->__commands as $command) {
            $command->execute();
        }
    }
}

// 命令1 只执行action-1
class ConcreteCommand1 implements Command
{
    private $__receiver = null;

    public function __construct(Receiver $receiver)
    {
        $this->__receiver = $receiver;
    }

    // 只执行action-1  执行接受者特定的命令
    public function execute()
    {
        $this->__receiver->action_1();
    }
}

// 命令2 只执行action_2
class ConcreteCommand2 implements Command
{
    private $__receiver = null;

    public function __construct(Receiver $receiver)
    {
        $this->__receiver = $receiver;
    }

    // 执行action_2
    public function execute()
    {
        $this->__receiver->action_2();
    }
}

// 设置三个接受者 都拥有action_1 action_2
$recv_1 = new Receiver('recv_1');
$recv_2 = new Receiver('recv_2');
$recv_3 = new Receiver('recv_3');

//给命令对象添加接受者
$cmd_11 = new ConcreteCommand1($recv_1);// recv_1 action_1
$cmd_12 = new ConcreteCommand1($recv_2);// recv_2 action_1
$cmd_21 = new ConcreteCommand2($recv_1);// recv_1 action_2
$cmd_22 = new ConcreteCommand2($recv_2);// recv_2 action_2


// 执行者
$invoker = new Invoker();
$invoker->setCommand($cmd_11);
$invoker->execCommand();
$invoker->setCommand($cmd_12);
$invoker->execCommand();
$invoker->setCommand($cmd_21);
$invoker->delCommand($cmd_11);
$invoker->execCommand();
$invoker->setCommand($cmd_22);
$invoker->execCommand();
