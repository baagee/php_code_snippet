<?php

/**
 * Desc:用原型实例指定创建对象的种类，并且通过拷贝这些原型创建新的对象。
 * Prototype原型模式是一种创建型设计模式，Prototype模式允许一个对象再创建另外一个可定制的对象，根本无需知道任何如何创建的细节,
 * 工作原理是:通过将一个原型对象传给那个要发动创建的对象，这个要发动创建的对象通过请求原型对象拷贝它们自己来实施创建。
 * 用原型实例指定创建对象的种类.并且通过拷贝这个原型来创建新的对象
 * User: baagee()
 * Date: 2018/8/21
 * Time: 上午10:27
 */
abstract class Prototype
{
    protected $_id = 0;

    public function __construct($id)
    {
        $this->_id = $id;
    }

    public function getId()
    {
        echo $this->_id . PHP_EOL;
    }

    public function __clone()
    {
        $this->_id += 1;
    }

    public function getClone()
    {
        return clone $this;
    }
}

class ConcretePrototype extends Prototype
{

}

$c1 = new ConcretePrototype(1);
$c1->getId();
$c2 = $c1->getClone();
$c2->getId();
$c3=$c2->getClone();
$c3->getId();