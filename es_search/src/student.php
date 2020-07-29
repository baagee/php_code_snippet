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

$client = Elasticsearch\ClientBuilder::create()->setHosts(['localhost:9200'])->build();

$table = \BaAGee\MySQL\FasterTable::getInstance('student_score');


$works = ['程序员', '搬砖', '厨师', '挖掘机', '老师', '医生', '售货员', '司机'];
$hobbies = ['打篮球', '打羽毛球', '游泳', '打乒乓球', '看电影', '玩游戏', '写作', '看小说', '踢足球', '做美食'];

$address = [
    "徐汇区虹漕路461号58号楼5楼", "泉州市洛江区万安塘西工业区", "朝阳区北苑华贸城",
    "广州国际采购中心1401",
    "上海市长宁区金钟路658弄5号楼5楼",
    "徐汇区虹漕路461号58号楼5楼",
    "济南市历下区和平路34号轻骑院内东二层山东朵拉",
    "湖北省武汉市洪山区",
    "湖北省恩施土家族苗族自治州恩施市",
    "北京市市辖区朝阳区",
    "内蒙古自治区兴安盟科尔沁右翼前旗",
    "西藏自治区日喀则地区日喀则市",
    "海南省省直辖县级行政单位中沙群岛的岛礁及其海域",
];
$list = $table->yieldRows(['is_delete' => ['=', 0]]);
foreach ($list as $item) {
    // unset($item['is_delete']);
    // $params = [
    //     'body' => $item,
    //     'id' => $item['id'],
    //     'index' => 'student',
    //     'type' => 'grades'
    // ];

    $t = mt_rand(1, count($hobbies) - 3);
    $hh = [];
    for ($i = 0; $i <= $t; $i++) {
        $hh[] = $hobbies[mt_rand(0, count($hobbies) - 1)];
    }
    $hh = array_unique($hh);
    $params = [
        'body' => [
            'student_name' => $item['student_name'],
            'student_id' => $item['student_id'],
            'create_time' => time(),
            'update_time' => 0,
            'hometown' => $address[mt_rand(0, count($address) - 1)],
            'hobby' => implode(',', $hh),
            'phone' => mt_rand(11111111111, 19000000000),
            'work' => $works[mt_rand(0, count($works) - 1)],
            'email' => mt_rand(10000, 999999999) . '@test.com',
        ],
        'id' => $item['id'],
        'index' => 'student_info',
        'type' => 'info'
    ];
    try {
        $res = $client->index($params);
        echo 'success ' . $item['id'] . PHP_EOL;
    } catch (Exception $e) {
        echo 'Error:' . $e->getMessage() . PHP_EOL;
    }
}

echo 'over';
die;



