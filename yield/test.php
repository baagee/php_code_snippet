<?php

/**
 * Desc: 关于PHP yield http://www.laruence.com/2015/05/28/3038.html
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/7/25
 * Time: 下午3:46
 */
class Task
{
    protected $_taskId = null;
    protected $_coroutine = null;
    protected $_sendValue = null;
    protected $_fistYield = true;

    /**
     * Task constructor.
     * @param int $task_id 任务ID
     * @param Generator $coroutine
     */
    public function __construct($task_id, Generator $coroutine)
    {
        $this->_taskId = $task_id;
        $this->_coroutine = $coroutine;
    }

    /**
     * 设置send value
     * @param $value
     */
    public function setSendValue($value)
    {
        $this->_sendValue = $value;
    }

    /**
     * 任务运行
     * @return mixed
     */
    public function run()
    {
        if ($this->_fistYield) {
            // 第一次yield
            $this->_fistYield = false;
            return $this->_coroutine->current();
        } else {
            // 中断后接着执行 value 赋值给上次yield的地方然后，自动指针后移，调用current
            $return = $this->_coroutine->send($this->_sendValue);
            $this->_sendValue = null;
            return $return;
        }
    }

    /**
     * 获取任务ID
     * @return null
     */
    public function getTaskId()
    {
        return $this->_taskId;
    }

    /**
     * 判断任务是否结束
     * @return bool
     */
    public function isFinished()
    {
        return !$this->_coroutine->valid();
    }
}

class Schedule
{
    protected $_maxTaskId = 0;
    protected $_taskMap = [];// taskId=>Task
    // 任务队列
    protected $_taskQueue = null;

    public function __construct()
    {
        $this->_taskQueue = new SplQueue();
    }

    /**
     * 向调度器添加新任务
     * @param Generator $coroutine yield任务
     * @return int
     */
    public function addNewTask(Generator $coroutine)
    {
        $taskId = ++$this->_maxTaskId;
        $task = new Task($taskId, $coroutine);
        $this->_taskMap[$taskId] = $task;
        $this->schedule($task);
        return $taskId;
    }

    /**
     * 向队列尾部添加任务
     * @param Task $task
     */
    public function schedule(Task $task)
    {
        $this->_taskQueue->enqueue($task);
    }

    public function run()
    {
        // 当队列中有任务时
        while (!$this->_taskQueue->isEmpty()) {
            $task = $this->_taskQueue->dequeue();// 取出一个队列
            $res = $task->run();
            if ($res instanceof SystemCall) {
                // 如果task返回值属于系统调用类型的
                $res($task, $this);
                continue;
            }
            if ($task->isFinished()) {
                // 任务结束了，从调度器删除
                unset($this->_taskMap[$task->getTaskId()]);
            } else {
                // 任务继续进入调度器
                $this->schedule($task);
            }
        }
    }

    /*
     * 杀死一个任务进程
     */
//    public function killTask($taskId)
//    {
//        if (!isset($this->_taskMap[$taskId])) {
//            return false;
//        }
//        unset($this->_taskMap[$taskId]);
//        foreach ($this->_taskQueue as $i => $task) {
//            if ($task->getTaskId() == $taskId) {
//                unset($this->_taskQueue[$i]);
//                break;
//            }
//        }
//        return true;
//    }
}


class SystemCall
{
    protected $_callback = null;

    public function __construct(callable $callback)
    {
        $this->_callback = $callback;
    }

    public function __invoke(Task $task, Schedule $schedule)
    {
        $callback = $this->_callback;
        return $callback($task, $schedule);
    }
}

// 任务1
function task_1()
{
    for ($i = 1; $i <= 9; $i++) {
        echo 'task_1    $i=' . $i . PHP_EOL;
        yield;
    }
}

// 任务2
function task_2()
{
    for ($j = 1; $j < 5; $j++) {
        echo 'task_2    $j=' . $j . PHP_EOL;
        yield;
    }
}

function getTaskId()
{
    return new SystemCall(function (Task $task, Schedule $schedule) {
        $task->setSendValue($task->getTaskId());// 设置任务返回值为taskId
        $schedule->schedule($task);// 重新进入调度器
    });
}

function task($max)
{
    $taskId = yield getTaskId();
    for ($i = 1; $i <= $max; ++$i) {
        echo "This is task $taskId iteration $i" . PHP_EOL;
        yield;
    }
}
//
//$schedule = new Schedule();
//$schedule->addNewTask(task(5));
//$schedule->addNewTask(task(10));
//$schedule->run();

$schedule = new Schedule();
$schedule->addNewTask(task_1());
$schedule->addNewTask(task_2());
$schedule->run();