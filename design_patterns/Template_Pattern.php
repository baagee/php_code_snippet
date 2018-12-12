<?php

/**
 * Desc: 模板模式：定义一个操作中的算法骨架,而将一些步骤延迟到子类中,使得子类可以不改变一个算法的结构可以定义该算法的某些特定步骤，
 * 模板方法模式也是为了巧妙解决变化对系统带来的影响而设计的。使用模板方法使系统扩展性增强，最小化了变化对系统的影响
 * User: baagee()
 * Date: 2018/7/24
 * Time: 下午8:11
 */
abstract class TemplateBase
{
    public function method_1()
    {
        echo __METHOD__ . PHP_EOL;
    }

    public function method_2()
    {
        echo __METHOD__ . PHP_EOL;
    }

    public function doSomething()
    {
        $this->method_1();
        $this->method_2();
    }
}

class Template1 extends TemplateBase
{
    public function method_1()
    {
        echo 'Template1111111111' . PHP_EOL;
    }
}

class Template2 extends TemplateBase
{
    public function method_2()
    {
        echo 'Template2222222222' . PHP_EOL;
    }
}

$b = new Template1();
$b->doSomething();
$c = new Template2();
$c->doSomething();