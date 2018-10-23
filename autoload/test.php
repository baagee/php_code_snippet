<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/10/23
 * Time: 下午6:34
 */
include "./AutoLoader.php";

\Src\User::test1();
\Test\DB::connect();

\Top\Phone::call();
\Src\top\Address::get();

$add = add(1, 9);
var_dump($add);

echo today();