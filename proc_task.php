<?php
/**
 * Desc:
 * User: 01372412
 * Date: 2019/11/28
 * Time: 下午2:09
 */
$arr         = unserialize(stream_get_contents(STDIN));
$arr['time'] = date('Y-m-d H:i:s', $arr['time']);
sleep(1);
$arr['time1'] = date('Y-m-d H:i:s', time());
echo serialize($arr);
// throw new Exception('fdas');
exit(90);