<?php

/**
 * Desc:委托模式
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/7/31
 * Time: 上午10:14
 */
interface Player
{
    public function getPlayList($list);
}


class PlayerDelegation
{
    protected $_player = null;
    protected $_play_list = [];

    public function setPlayer($type)
    {
        $class = $type . 'Player';
        $this->_player = new $class();
        $this->_play_list = [];
    }

    public function addPlayList($item)
    {
        $this->_play_list[] = $item;
    }

    public function getPlayList()
    {
        return $this->_player->getPlayList($this->_play_list);
    }
}

class Mp3Player implements Player
{
    public function getPlayList($list)
    {
        foreach ($list as $item) {
            echo $item . PHP_EOL;
        }
    }
}

class RmvbPlayer implements Player
{
    public function getPlayList($list)
    {
        foreach ($list as $item) {
            echo $item . PHP_EOL;
        }
    }
}

$delegation = new PlayerDelegation();
$delegation->setPlayer('mp3');
$delegation->addPlayList('大海');
$delegation->addPlayList('红豆');
$delegation->addPlayList('雨蝶');
$delegation->getPlayList();
echo '----------------' . PHP_EOL;
$delegation->setPlayer('rmvb');
$delegation->addPlayList('蝙蝠侠');
$delegation->addPlayList('钢铁侠');
$delegation->addPlayList('蜘蛛侠');
$delegation->getPlayList();