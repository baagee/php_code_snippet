<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/10/23
 * Time: 下午10:35
 */
date_default_timezone_set('PRC');

include __DIR__ . "/../autoload/AutoLoader.php";


\Android\Oppo::run();

\Computer\windows\Lenovo::run();

\Computer\Apple::run();

\Phone\IPhone::run();

echo add(10, 90) . PHP_EOL;
echo today();