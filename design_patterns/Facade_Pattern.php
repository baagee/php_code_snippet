<?php
/**
 * Desc: 外观模式：外部与一个子系统的通信必须通过一个统一的外观对象进行，为子系统中的一组接口提供一个一致的界面，外观模式定义了一个高层接口，
 * 这个接口使得这一子系统更加容易使用。外观模式又称为门面模式，它是一种对象结构型模式。为子系统中的一组接口提供一个一致的界面,定义一个高层接口,
 * 使得这一子系统更加的容易使用。就是让client客户端以一种简单的方式来调用比较复杂的系统，来完成一件事情。
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/7/30
 * Time: 上午10:41
 */

class SubSystem1
{
    public function method1()
    {
        echo __METHOD__ . PHP_EOL;
    }
}

class SubSystem2
{
    public function method2()
    {
        echo __METHOD__ . PHP_EOL;
    }
}

class SubSystem3
{
    public function method3()
    {
        echo __METHOD__ . PHP_EOL;
    }
}

class Facade
{
    private $_object1 = null;
    private $_object2 = null;
    private $_object3 = null;

    public function __construct()
    {
        $this->_object1 = new SubSystem1();
        $this->_object2 = new SubSystem2();
        $this->_object3 = new SubSystem3();
    }

    public function methodA()
    {
        $this->_object1->method1();
        $this->_object2->method2();
    }

    public function methodB()
    {
        $this->_object2->method2();
        $this->_object3->method3();
    }
}

$f = new Facade();
$f->methodA();
$f->methodB();

echo '-----------------' . PHP_EOL;

// 示例
class Car
{
    public function checkStop()
    {
        echo '检查刹车' . PHP_EOL;
    }

    public function checkBox()
    {
        echo '检查油箱' . PHP_EOL;
    }

    public function checkConsole()
    {
        echo '检查仪表盘' . PHP_EOL;
    }

    public function go()
    {
        echo '车子启动' . PHP_EOL;
    }
}

class CarFacade
{
    public function carGo(Car $car)
    {
        $car->checkBox();
        $car->checkStop();
        $car->checkConsole();
        $car->go();
    }
}

$car = new Car();
$facade = new CarFacade();
$facade->carGo($car);