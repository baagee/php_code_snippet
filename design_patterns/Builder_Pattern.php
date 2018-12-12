<?php

/**
 * Desc:建造者模式 将一个复杂对象的构建与它的表示分离,使用同样的构建过程可以创建不同的表示
 * User: baagee()
 * Date: 2018/8/8
 * Time: 上午10:33
 */
class Product
{
    public $color = '';
    public $size = '';
    public $name = '';

    public function __construct($color, $size, $name)
    {
        $this->name = $name;
        $this->size = $size;
        $this->color = $color;
    }

    public function info()
    {
        foreach ($this as $item) {
            var_dump($item);
        }
    }
}

/*不使用建造者*/
$p = new Product('紫色', '12', '发放');
$p->info();

echo '-------------------------' . PHP_EOL;

class ProductBuild
{
    protected $_obj = null;
    protected $_config = [];

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function build()
    {
        $this->_obj = new Product($this->_config['color'], $this->_config['size'], $this->_config['name']);
    }

    public function getProduct(): Product
    {
        return $this->_obj;
    }
}

$pb = new ProductBuild([
    'color' => '白色',
    'size' => 22,
    'name' => '可可'
]);

$pb->build();
$pp = $pb->getProduct();
$pp->info();