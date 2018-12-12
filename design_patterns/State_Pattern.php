<?php
/**
 * Desc: 状态模式
 * 允许一个对象在类内部状态改变时改变它的行为，对象看起来似乎修改了他的类。是“接口—实现类”这种模式的应用，是面向接口编程原则的体现。
 * 状态模式是一种对象行为模式，封装了转换规则。
 * 把状态的判断转移表示到不同状态一系列类中，可以方便的增加新的状态，只要改变对象状态就可以改变对象行为。
 * 可以让多个环境对象共享同一个状态对象，从而减少系统中状态的个数。
 * User: baagee()
 * Date: 2018/7/22
 * Time: 下午7:18
 */

/*
 * 一只猪的日常，每天就三件事，吃饭，睡觉，起床，吃饭，睡觉，起床......
*/

/**
 * Interface State 定义状态接口
 */
interface State
{
    public function setNextState(Pig $pig);

    public function action();
}

/*
 * 定义吃饭状态
 */
class Eat implements State
{
    public function setNextState(Pig $pig)
    {
        // 切换睡觉状态状态
        $pig->setState(new Sleep());
    }

    public function action()
    {
        echo "吃饭" . PHP_EOL;
    }
}

/*
 * 定义睡觉状态
 */
class Sleep implements State
{
    public function setNextState(Pig $pig)
    {
        // 切换起床状态
        $pig->setState(new GetUp());
    }

    public function action()
    {
        echo "睡觉" . PHP_EOL;
    }
}

/*
 * 定义起床状态
 */
class GetUp implements State
{
    public function setNextState(Pig $pig)
    {
        // 切换吃饭状态
        $pig->setState(new Eat());
    }

    public function action()
    {
        echo "起床" . PHP_EOL;
    }
}

/*
 * 一头猪
 */
class Pig
{
    // 初始状态
    private $__state = null;

    public function __construct($state)
    {
        $this->setState($state);
    }

    /*
     * 切换状态
     */
    public function setState($state)
    {
        $this->__state = $state;
    }

    public function doAction()
    {
        $this->__state->action();
        // 设置下一个动作
        $this->__state->setNextState($this);
    }
}

// 实例化一头猪
$pig = new Pig(new Sleep());
// 这头猪每天
for ($i = 1; $i <= 6; $i++) {
    $pig->doAction();
}
/*
 * 运行结果：
 * /usr/local/php/bin/php /home/php_code_test/design_patterns/State_Pattern.php
睡觉
起床
吃饭
睡觉
起床
吃饭
 * */
