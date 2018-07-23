<?php
/**
 * Desc: 代理模式，就是给某一对象提供代理对象，并由代理对象控制具体对象的引用
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/7/23
 * Time: 上午11:05
 */
/*代理接口*/
interface Proxy
{
    public function produce();

    public function sell();
}

/*真实公司*/
class RealCompany implements Proxy
{
    // 生产
    public function produce()
    {
        echo 'real produce' . PHP_EOL;
    }

    // 销售
    public function sell()
    {
        echo 'real sell' . PHP_EOL;
    }
}

/*代理公司*/
class ProxyCompany implements Proxy
{
    /*保存要代理真实的公司*/
    private $__real = null;

    public function __construct()
    {
        $this->__real = new RealCompany();
    }

    // 生产
    public function produce()
    {
        $this->__real->produce();
    }

    /*销售*/
    public function sell()
    {
        $this->__real->sell();
    }
}

$pc = new ProxyCompany();
$pc->produce();
$pc->sell();
/*代理模式的工作方式：首先，因为代理和真实都实现了共同的接口，这使我们可以在不改变原来接口的情况下，只要用真实对象的地方，
都可以用代理来代替。其次，代理在客户和真实之间起了一个中介作用，利用这个中介平台，我们可以在把客户请求传递给真实之前做一些必要的预处理。*/