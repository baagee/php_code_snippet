<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/6/7
 * Time: 上午10:13
 */

class AccountUser implements SplSubject
{
    /**
     * @var int
     */
    protected $loginNum = 0;
    /**
     * @var string
     */
    protected $hobby = '';

    /**
     * @return int
     */
    public function getLoginNum(): int
    {
        return $this->loginNum;
    }

    /**
     * @return string
     */
    public function getHobby(): string
    {
        return $this->hobby;
    }

    /**
     * @var SplObjectStorage
     */
    protected $observes = [];

    /**
     * AccountUser constructor.
     * @param $hobby
     */
    public function __construct($hobby)
    {
        $this->loginNum = mt_rand(1, 10);
        $this->hobby = $hobby;
        $this->observes = new SplObjectStorage();
    }

    /**
     *
     */
    public function login()
    {
        echo '登陆次数加一' . PHP_EOL;
        $this->loginNum++;//登陆次数加一
        echo '当前登陆次数：' . $this->loginNum . PHP_EOL;
        //通知观察者
        $this->notify();
    }


    /**
     * 添加观察者
     * @param SplObserver $observer
     */
    public function attach(SplObserver $observer)
    {
        $this->observes->attach($observer);
    }

    /**
     * 移除观察者
     * @param SplObserver $observer
     */
    public function detach(SplObserver $observer)
    {
        $this->observes->detach($observer);
    }

    /**
     *
     */
    public function notify()
    {
        $this->observes->rewind();
        while ($this->observes->valid()) {
            //执行观察者的update
            $this->observes->current()->update($this);
            $this->observes->next();
        }
    }
}

/**
 * 安全观察者
 * Class Secure
 */
class Secure implements SplObserver
{
    /**
     * @param SplSubject $subject
     */
    public function update(SplSubject $subject)
    {
        if ($subject->getLoginNum() > 5) {
            echo '登陆次数超过5次,发出报警' . PHP_EOL;
        }
    }
}

/**
 * 广告推荐观察者
 * Class AdRecommend
 */
class AdRecommend implements SplObserver
{
    /**
     * @param SplSubject $subject
     */
    public function update(SplSubject $subject)
    {
        switch ($subject->getHobby()) {
            case "打游戏":
                echo '爱好是打游戏 推荐游戏广告' . PHP_EOL;
                break;
            case "看电影":
                echo '爱好是看电影 推荐好看的电影' . PHP_EOL;
                break;
            default:
                break;
        }
    }
}


$user = new AccountUser('打游戏');

$user->attach(new Secure());
$user->attach(new AdRecommend());
$user->login();


$user = new AccountUser('看电影');
$s1 = new Secure();
$user->attach($s1);
$user->attach(new AdRecommend());
$user->detach($s1);
$user->login();
