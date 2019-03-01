<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/2/2
 * Time: 下午2:25
 */

echo __LINE__ . ': ob_level=' . ob_get_level() . '<br>';//1

ob_start();
echo time().'<br>';
echo __LINE__ . ': ob_level=' . ob_get_level() . '<br>';//1

ob_start();
echo '<br>222<br>';
echo __LINE__ . ': ob_level=' . ob_get_level() . '<br>';//1

$two=ob_get_contents();
ob_end_flush();
var_dump($two);
// ob_clean();//清空缓冲区，不关闭ob

//ob_end_clean();// 清空，不输出，关闭ob

// $buf=ob_get_clean();//获取缓冲内容。关闭
// var_dump($buf);

// ob_flush();//输出到浏览器，不关闭ob
// ob_end_flush();// 输出到浏览器 关闭ob

// $buf=ob_get_flush();//输出到浏览器 返回内容 关闭ob
// var_dump($buf);
echo __LINE__ . ': ob_level=' . ob_get_level() . '<br>';

echo '<br>end';

