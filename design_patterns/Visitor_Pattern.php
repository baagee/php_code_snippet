<?php

/**
 * Desc: 访问者模式 Visitor表示一个作用于某对象结构中的各元素的操作,可以在不改变各元素的类的前提下定义作用于这些元素的新操作
 * User: baagee()
 * Date: 2018/8/10
 * Time: 下午8:42
 */
abstract class Visitor
{
    abstract public function visitElementA($element);

    abstract public function visitElementB($element);
}

// 俩访问者
class VisitorA extends Visitor
{
    public function visitElementA($element)
    {
        echo get_class($element) . __METHOD__ . PHP_EOL;
    }

    public function visitElementB($element)
    {
        echo get_class($element) . __METHOD__ . PHP_EOL;
    }
}

class VisitorB extends Visitor
{
    public function visitElementA($element)
    {
        echo get_class($element) . __METHOD__ . PHP_EOL;
    }

    public function visitElementB($element)
    {
        echo get_class($element) . __METHOD__ . PHP_EOL;
    }
}

abstract class Element
{
    abstract function accept(Visitor $visitor);
}

// 俩元素
class ElementA extends Element
{
    public function accept(Visitor $visitor)
    {
        $visitor->visitElementA($this);
    }
}

class ElementB extends Element
{
    public function accept(Visitor $visitor)
    {
        $visitor->visitElementB($this);
    }
}

class ObjectStructure
{
    private $elements = [];

    public function add(Element $element)
    {
        $this->elements[] = $element;
    }

    public function del(Element $element)
    {
        $key = array_search($element, $this->elements);
        if ($key !== false) {
            unset($this->elements[$key]);
        }
    }

    public function accept(Visitor $visitor)
    {
        foreach ($this->elements as $e) {
            $e->accept($visitor);
        }
    }
}

$os = new ObjectStructure();
$os->add(new ElementA());
$os->add(new ElementB());
$va = new VisitorA();
$vb = new VisitorB();
$os->accept($va);
$os->accept($vb);