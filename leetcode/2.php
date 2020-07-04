<?php
/**
 * 给定一个数组和一个目标和，从数组中找两个数字相加等于目标和，输出这两个数字的下标。
 * [2,4,3]+[5,6,4]=708
 * 342+465=807
 */


$x = [2, 4, 3];
$y = [5, 6, 4, 2];

$res = [];

if (count($x) < count($y)) {
    function swap($x, $y)
    {
        return [$y, $x];
    }

    [$x, $y] = swap($x, $y);
}

foreach ($x as $i => $v1) {
    $sum = $v1 + ($y[$i] ?? 0);
    if ($sum >= 10) {
        $v = $sum % 10;
        $c = 1;
    } else {
        $v = $sum;
        $c = 0;
    }
    if (isset($res[$i + 1])) {
        $res[$i + 1] += $c;
    } elseif (isset($x[$i + 1])) {
        $res[$i + 1] = $c;
    }
    $res[$i] = ($res[$i] ?? 0) + $v;
    // echo json_encode($res) . PHP_EOL;
    // echo str_repeat('#', 100) . PHP_EOL;
}
ksort($res);
var_dump($res);
