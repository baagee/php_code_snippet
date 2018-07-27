<?php

/**
 * Desc: 解释器模式 给定一种语言，定义他的文法的一种表示，并定义一个解释器，该解释器使用该表示来解释语言中句子
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/7/26
 * Time: 下午1:18
 */
// 抽象表达式
abstract class Expression
{
    public function interpreter($str)
    {
    }
}

// 数字表达式
class NumberExpression extends Expression
{
    public function interpreter($str)
    {
        switch ($str) {
            case "0":
                return "零";
            case "1":
                return "一";
            case "2":
                return "二";
            case "3":
                return "三";
            case "4":
                return "四";
            case "5":
                return "五";
            case "6":
                return "六";
            case "7":
                return "七";
            case "8":
                return "八";
            case "9":
                return "九";
        }
    }
}

// 字符串表达式
class StrExpression extends Expression
{
    public function interpreter($str)
    {
        return strtoupper($str);
    }
}

// 解释器
class Interpreter
{
    public function execute($str)
    {
        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str{$i};
            if (is_numeric($char)) {
                $expression = new NumberExpression();
            } else {
                $expression = new StrExpression();
            }
            echo $expression->interpreter($char);
        }
    }
}

$i=new Interpreter();
// 将字符串转为大写的
$i->execute('1235435hiusvfbgwer9876543');