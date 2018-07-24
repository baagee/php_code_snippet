<?php

/**
 * Desc:
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/7/23
 * Time: 上午11:44
 */

/*Trait 是为类似 PHP 的单继承语言而准备的一种代码复用机制。Trait 为了减少单继承语言的限制，使开发人员能够自由地在不同层次结构内独立的类中复用 method。Trait 和 Class 组合的语义定义了一种减少复杂性的方式，避免传统多继承和 Mixin 类相关典型问题。

Trait 和 Class 相似，但仅仅旨在用细粒度和一致的方式来组合功能。 无法通过 trait 自身来实例化。它为传统继承增加了水平特性的组合；也就是说，应用的几个 Class 之间不需要继承。*/

trait test1
{
    function method_1()
    {
        echo 'test1 method_1' . PHP_EOL;
    }

    function method_2($age)
    {
        echo 'test1 method_2  age=' . $age . PHP_EOL;
    }
}

class A
{
    use test1;

    function method_1()
    {
        echo 'A method_1' . PHP_EOL;
    }
}

class B extends A
{
    use test1;

    function method_2($age)
    {
        echo 'B method_2 age=' . $age . PHP_EOL;
    }

    function method_3()
    {
        echo 'B method_3' . PHP_EOL;
    }
}

$a = new A();

$a->method_1();
$a->method_2(34);

$b = new B();
$b->method_1();
$b->method_2(12);
$b->method_3();
// 方法覆盖关系：子类>trait>父类，即子类的方法（method_2）会覆盖trait的(method_2)方法，trait的方法(method_1)会覆盖其继承父类的方法(method_1)


/*可以在一个类中引用多个Trait。引用多个Trait的时候,就容易出问题了,最常见的问题就是两个Trait中如果出现了同名的属性或者方法该怎么办呢?这个时候就需要用到Insteadof 和 as 这两个关键字了.请看如下实现代码*/

trait test2
{
    function method_1()
    {
        echo 'test2 method_1' . PHP_EOL;
    }

    function method_2()
    {
        echo 'test2 method_2' . PHP_EOL;
    }
}

class C
{
    use test1, test2 {
        test1::method_1 insteadof test2;// 前者test1覆盖后者test2
        test2::method_2 insteadof test1;// 前者test2覆盖后者test1
        test2::method_2 as t2m2;// 给test2::method_2起别名
    }

    function method_3()
    {
        echo 'C method_3' . PHP_EOL;
    }
}

echo '-----------------' . PHP_EOL;
$c = new C();
$c->method_1();
$c->method_2();
$c->method_3();
$c->t2m2();

// 结果：
/*
 * sftp://usr/local/php/bin/php /home/trait/test1.php
A method_1
test1 method_2  age=34
test1 method_1
B method_2 age=12
B method_3
-----------------
test1 method_1
test2 method_2
C method_3
test2 method_2*/