<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/3/26
 * Time: 下午3:28
 */
include __DIR__ . '/../vendor/autoload.php';


$client = Elasticsearch\ClientBuilder::create()->setHosts(['localhost'])->build();


$list = [
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => '是德国的时候，是电饭锅 v 鳄梨UI'
    ],
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => '激励太美顺丰大哥说的时间到了开发公司好看iu 额外日哦但是 v 你会发觉阿克苏河公示牌sad hi u 哈哈'
    ],
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => 'asdfs塑料袋封口机来上课还将额外 i 鹿哥我'
    ],
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => '中国驻洛杉矶领事馆遭亚裔男子枪击 嫌犯已自首'
    ],
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => '中韩渔警冲突调查：韩警平均每天扣1艘中国渔船'
    ],
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => '公安部：各地校车将享最高路权'
    ],
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => '美国留给伊拉克的是个烂摊子吗'
    ],
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => '是德国进口方式而哦 i 各位哦好吗蓼莪 hi 俄 u'
    ], [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => '收到回复个 i 收到i 为了他因为 u 好也陆海空军'
    ],
    [
        'id' => mt_rand(10000, PHP_INT_MAX),
        'title' => 'sfd你快结束的哪个教室记得看过狐假虎威哦和 i 如何'
    ],
];
// 添加
/*foreach ($list as $item) {
    $params = [
        'body' => [
            'id' => $item['id'],
            'title' => $item['title'],
        ],
        'id' => $item['id'],
        'index' => 'test1',
        'type' => 'title'
    ];
    try {
        $res = $client->index($params);
        // var_dump($res);
        var_dump('success');
    } catch (Exception $e) {
        echo 'Error:' . $e->getMessage() . PHP_EOL;
    }
}*/

// 中文分词搜索
$serparams = [
    'index' => 'test1',
    'type' => 'title',
    'body' => [
        'query' => [
            'match' => [
                'title' => '激励太美顺丰大哥'
            ]
        ]
    ],
    'from' => 0,//page
    'size' => 20//size
];

// $serparams['body']['query']['match']['title'] = 'SELECT';
$resech = $client->search($serparams);

$hits = $resech['hits'];
$total = $hits['total'];
$list = $hits['hits'];
var_dump($total, $list);

die;
//
//
// $resech = $client->get([
//     'index' => 'sql',
//     'type' => 'title',
//     'id' => 1582862272207580
// ]);
// echo $resech['_source']['sql'];
echo 'OVER' . PHP_EOL;



