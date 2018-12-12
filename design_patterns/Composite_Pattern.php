<?php
/**
 * Desc: 组合模式 将对象组合成树形结构以表示"部分-整体"的层次结构,使得客户对单个对象和复合对象的使用具有一致性
 * User: baagee()
 * Date: 2018/7/22
 * Time: 下午8:32
 */

// 定义食品组件
abstract class FoodComponent
{
    public function add($component)
    {
    }

    public function remove($component)
    {
    }

    public function getName()
    {
    }

    public function getUrl()
    {
    }

    public function display()
    {
    }
}

// 大的分类组件
class Food extends FoodComponent
{
    private $__items = array();
    private $__name = null;

    public function __construct($name)
    {
        $this->__name = $name;
    }

    // 可以增加水果
    public function add($component)
    {
        $this->__items[] = $component;
    }

    // 删除水果
    public function remove($component)
    {
        $key = array_search($component, $this->__items);
        if ($key !== false) unset($this->__items[$key]);
    }

    // 显示水果
    public function display()
    {
        echo "大分类：" . $this->__name . PHP_EOL;
        foreach ($this->__items as $item) {
            // 显示小组件
            $item->display();
        }
    }
}

// 一个小的水果分类
class Item extends FoodComponent
{
    private $__name = null;
    private $__color = null;

    public function __construct($name, $color)
    {
        $this->__name = $name;
        $this->__color = $color;
    }

    public function display()
    {
        // 显示小的分类
        echo '一种' . $this->__color . '颜色的' . $this->__name . PHP_EOL;
    }
}

// 用户
class Client
{
    private $__food = null;

    public function __construct(FoodComponent $food)
    {
        $this->setFood($food);
    }

    // 设置链接
    public function setFood($food)
    {
        $this->__food = $food;
    }

    // 显示链接
    public function displayFood()
    {
        $this->__food->display();
    }
}

// 实例一下
$apple = new Food("苹果");
$banana = new Food("香蕉");
$grape = new Food("葡萄");

$item1 = new Item("绿苹果", "绿色");
$item2 = new Item("红苹果", "红色");
$apple->add($item1);
$apple->add($item2);

$item3 = new Item("黄香蕉", "黄色");
$item4 = new Item("绿香蕉", "绿色");
$banana->add($item3);
$banana->add($item4);

$allFruit = new Food("水果");
$allFruit->add($apple);
$allFruit->add($banana);
$allFruit->add($grape);
$objClient = new Client($allFruit);
$objClient->displayFood();

$objClient->setFood($apple);
$objClient->displayFood();
/*
组合模式的优点
    可以清楚地定义分层次的复杂对象，表示对象的全部或部分层次，使得增加新构件也更容易。
    客户端调用简单，客户端可以一致的使用组合结构或其中单个对象。
    定义了包含叶子对象和容器对象的类层次结构，叶子对象可以被组合成更复杂的容器对象，而这个容器对象又可以被组合，这样不断递归下去，可以形成复杂的树形结构。
    更容易在组合体内加入对象构件，客户端不必因为加入了新的对象构件而更改原有代码。

组合模式的缺点
    使设计变得更加抽象，对象的业务规则如果很复杂，则实现组合模式具有很大挑战性，而且不是所有的方法都与叶子对象子类都有关联。
    增加新构件时可能会产生一些问题，很难对容器中的构件类型进行限制。

模式适用环境
    在以下情况下可以使用组合模式：
    需要表示一个对象整体或部分层次，在具有整体和部分的层次结构中，希望通过一种方式忽略整体与部分的差异，可以一致地对待它们。
    让客户能够忽略不同对象层次的变化，客户端可以针对抽象构件编程，无须关心对象层次结构的细节。
    对象的结构是动态的并且复杂程度不一样，但客户需要一致地处理它们。*/