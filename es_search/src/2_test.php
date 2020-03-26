<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/26
 * Time: 下午3:28
 */
include __DIR__ . '/../vendor/autoload.php';

$config = include __DIR__ . '/db_config.example.php';

\BaAGee\MySQL\DBConfig::init($config);

$sqlDB = \BaAGee\MySQL\SimpleTable::getInstance('sql_detail');
// var_dump($sqlDB);
// die;

// $list = $sqlDB->fields(['*'])->where([
//     'cost' => ['>', 500]
// ])->limit(30)->select(false);
//
// // var_dump(\BaAGee\MySQL\SqlRecorder::getLastSql());
// var_dump($list[0]);
// die;
//


$client = Elasticsearch\ClientBuilder::create()->setHosts(['localhost'])->build();

// $client->indices()->delete(['index' => 'sql']);
// die;
// $client->indices()->create(['index' => 'sql',
//                             'type' => 'title']);
//
// die;

// 添加
/*foreach ($list as $item) {
    // var_dump($item);
    $params = [
        'body' => [
            'sql_id' => $item['s_id'],
            'sql' => $item['sql'],
        ],
        'id' => $item['s_id'],
        'index' => 'sql',
        'type' => 'title'
    ];
    try {
        $res = $client->index($params);
        var_dump($res);
    } catch (Exception $e) {
        echo 'Error:' . $e->getMessage() . PHP_EOL;
    }
}*/
//
//
$serparams = [
    'index' => 'sql',
    'type' => 'title',
    'body' => [
        'query' => [
            'match' => [
                'sql' => 'select'
            ]
        ]
    ],
    'from' => 0,//page
    'size' => 20//size
];

$serparams['body']['query']['match']['sql'] = 'SELECT';
$resech = $client->search($serparams);

$hits = $resech['hits'];
$total = $hits['total'];
$list = $hits['hits'];
var_dump($total, $list);

die;


$resech = $client->get([
    'index' => 'sql',
    'type' => 'title',
    'id' => 1582862272207580
]);
echo $resech['_source']['sql'];
echo 'OVER' . PHP_EOL;



