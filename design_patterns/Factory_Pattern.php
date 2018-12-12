<?php

/**
 * Desc: 工厂模式定义一个用于创建对象的接口,让子类决定将哪一个类实例化,使用一个类的实例化延迟到其子类
 * User: baagee()
 * Date: 2018/7/27
 * Time: 下午7:56
 */

// 创建数据库对象的工厂类
class DBFactory
{
    public static function create($dbTypeName)
    {
        switch ($dbTypeName) {
            case 'mysql':
                return new MySQLDB();
            case 'postage':
                return new PostageDB();
            case 'mssql':
                return new MySQLDB();
        }
    }
}

// 数据库类接口
interface DB
{
    public function connection();
}

class MySQLDB implements DB
{
    public function __construct()
    {
        $this->connection();
    }

    public function connection()
    {
        echo __CLASS__ . PHP_EOL;
    }
}

class MsSQLDB implements DB
{

    public function __construct()
    {
        $this->connection();
    }

    public function connection()
    {
        echo __CLASS__ . PHP_EOL;
    }
}

class PostageDB implements DB
{

    public function __construct()
    {
        $this->connection();
    }

    public function connection()
    {
        echo __CLASS__ . PHP_EOL;
    }
}

$db = DBFactory::create('mysql');
$db = DBFactory::create('mssql');
$db = DBFactory::create('postage');
