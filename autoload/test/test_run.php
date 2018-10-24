<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/10/23
 * Time: 下午10:35
 */
date_default_timezone_set('PRC');

include __DIR__ . "/../autoload/AutoLoader.php";

$t1 = microtime(true);
\Android\Oppo::run();

\Computer\windows\Lenovo::run();

\Computer\Apple::run();

\Phone\IPhone::run();

echo add(10, 90) . PHP_EOL;
echo today();
echo PHP_EOL . (microtime(true) - $t1) . PHP_EOL;
// 缓存起来:    0.00026392936706543
// 不缓存:      0.0034389495849609