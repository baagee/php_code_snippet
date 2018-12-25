<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/24
 * Time: 下午4:07
 */


$cookie = 'safedog-flow-item=A69965EE6BC4EA050B40BBD2EE22C720; Hm_lvt_28cdd27bf4aafcb5d46ef0217752b641=1545668662; Hm_lpvt_28cdd27bf4aafcb5d46ef0217752b641=1545668662';

$cookies = [];
foreach (explode('; ', $cookie) as $item) {
    $item              = explode('=', $item);
    $cookies[$item[0]] = $item[1];
}
var_dump($cookies);
