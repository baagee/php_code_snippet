<?php

/**
 * Desc:观察者模式，当对象间存在一对多关系时，则使用观察者模式（Observer Pattern）。比如，当一个对象被修改时，则会自动通知它的依赖对象。观察者模式属于行为型模式。
 * User: baagee()
 * Date: 2018/7/25
 * Time: 下午8:54
 */
class ObserverAble
{
    private $_observers = array();

    public function registerObserver($observer)
    {
        $this->_observers[] = $observer;
    }

    public function removeObserver($observer)
    {
        $key = array_search($observer, $this->_observers);
        if (!($key === false)) {
            unset($this->_observers[$key]);
        }
    }

    public function notifyObservers()
    {
        foreach ($this->_observers as $observer) {
            if ($observer instanceof Observer) $observer->update($this);
        }
    }
}

interface Observer
{
    public function update($observer);
}

interface DisplayElement
{
    public function display();
}

// 新闻观察者 观察运动和本地
class NewsObserverAble extends ObserverAble
{
    private $_sports_news;

    // 设置运动新闻，通知其他观察者
    public function setSportsNews($data)
    {
        $this->_sports_news = $data;
        $this->notifyObservers();
    }

    public function getSportsNews()
    {
        return $this->_sports_news;
    }

    private $_local_news;

    // 设置本地新闻
    public function setLocalNews($data)
    {
        $this->_local_news = $data;
        $this->notifyObservers();
    }

    public function getLocalNews()
    {
        return $this->_local_news;
    }
}

// 运动新闻
class SportsNews implements Observer, DisplayElement
{
    private $_data = null;

    public function update($observer)
    {
        if ($this->_data != $observer->getSportsNews()) {
            $this->_data = $observer->getSportsNews();
            $this->display();
        }
    }

    public function display()
    {
        echo $this->_data . date("Y-m-d H:i:s") . PHP_EOL;
    }
}

// 本地新闻
class LocalNews implements Observer, DisplayElement
{
    private $_data = null;

    public function update($observer)
    {
        if ($this->_data != $observer->getLocalNews()) {
            $this->_data = $observer->getLocalNews();
            $this->display();
        }
    }

    public function display()
    {
        echo $this->_data . date("Y-m-d H:i:s") . PHP_EOL;
    }
}

// -- 实例化 ---
$objObserver = new NewsObserverable();
// 被观察对象
$local = new LocalNews();
$sports = new SportsNews();

// 观察者里注册观察对象
$objObserver->registerObserver($local);
$objObserver->registerObserver($sports);

$objObserver->setSportsNews("sports news 1 ");
$objObserver->setLocalNews("local news 1 ");
$objObserver->removeObserver($sports);
$objObserver->setLocalNews("local news 2 ");
$objObserver->setSportsNews("sports news 2 ");
$objObserver->removeObserver($local);
$objObserver->setLocalNews("local news 3 ");