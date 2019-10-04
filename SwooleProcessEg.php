<?php
ini_set('display_errors', 1);

$startTime = microtime(true);

$max = 100;

$worker = [];
for ($i = 1; $i <= $max; $i++) {
    $process = new \Swoole\Process(function (\Swoole\Process $process) {
        $readData = $process->read(PHP_INT_MAX);
        $readData = json_decode($readData, true);
        usleep(45000);
        $process->write(json_encode([
            'dateTime' => str_repeat(date('Y-m-d H:i:s', $readData['timeStamp']), 2000)
        ]));
    }, false, true);
    $process->write(json_encode([
        'timeStamp' => time() + mt_rand(100, 999)
    ]));
    $pid          = $process->start();
    $worker[$pid] = $process;
}
foreach ($worker as $index => $w) {
    echo 'data:' . $w->read(PHP_INT_MAX) . PHP_EOL;
}


// for ($i = 0; $i <= $max; $i++) {
//     $process = new \Swoole\Process(function (\Swoole\Process $process) {
//         sleep(1);
//         $readData = $process->read();
//         $readData = json_decode($readData, true);
//         $process->write(json_encode([
//             'dateTime' => date('Y-m-d H:i:s', $readData['timeStamp'])
//         ]));
//     }, false, true);
//     $process->write(json_encode([
//         'timeStamp' => time() + mt_rand(100, 999)
//     ]));
//     $pid     = $process->start();
//     swoole_event_add($process->pipe, function ($pipe) use ($process) {
//         $readData = $process->read(PHP_INT_MAX);
//         echo 'data:' . $readData . PHP_EOL;
//     });
// }


$endTime = microtime(true);
echo 'OVER use time:' . (($endTime - $startTime) * 1000) . 'ms' . PHP_EOL;