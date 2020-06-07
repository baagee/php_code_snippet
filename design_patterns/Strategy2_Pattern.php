<?php

/**
 * Desc: 策略模式定义一系列算法,把它们一个个封装起来,并且使它们可相互替换,使用得算法的变化可独立于使用它的客户
 * User: baagee()
 * Date: 2018/7/26
 * Time: 上午10:12
 */

interface  Math
{
    public function calc($num1, $num2);
}

class Add implements Math
{
    public function calc($num1, $num2)
    {
        return $num1 + $num2;
    }
}

class Div implements Math
{
    public function calc($num1, $num2)
    {
        return $num1 / $num2;
    }
}

class CMath
{
    public static function calc($num1, $oper, $num2)
    {
        $c = strtoupper($oper);
        return (new $c())->calc($num1, $num2);
    }
}

$res = CMath::calc(1, 'add', 2);
var_dump($res);


$res = CMath::calc(10, 'div', 2);
var_dump($res);
