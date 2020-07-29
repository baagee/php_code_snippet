<?php
/**
 * Desc:
 * User: baagee
 * Date: 2020/7/29
 * Time: 下午4:58
 */
include __DIR__ . '/../vendor/autoload.php';

$query = new \BaAGee\SimpleDsl\BoolQuery();
$query->mustMatch('hometown', '海南省');
$query->mustTerms('work', [
    "医生", "老师"
]);
$query->mustLike('email', '*@test.com');
$query->mustMatch('hobby', '打篮球，做美食', 'or');

$query->notTerm('student_id', 1594182044065);
$query->notTerm('student_id', 1593457121399);
$query->notTerms('student_id', [1593016393864, 1593015907427]);
$query->notLike('phone', '1505143*');
$query->notMatch('hometown', '湖北省武汉市');
$query->mustTerm('work', '医生');
$query->mustRange('student_id', ['gte' => 1565324861568]);

$dsl = new \BaAGee\SimpleDsl\ConvertDSL($query);
$dsl->highlight(['hometown']);
$dsl->sort(['student_id' => 'desc']);
$dsl->offset(0);
$dsl->limit(100);
$dsl->sourceInclude([
    "student_id",
    "student_name",
    "work",
    "hobby",
    "hometown",
    "phone"
]);
$dsl->sourceExcludes([
    "update_time"
]);
echo $dsl->getDslJson(true);


/*where (hobby =打篮球 and hobby = 做美食 and work in (医生，老师) and student_id!=1565252207417 and
(student_in >1565245896038 and student_id<=1565364034570 or phone=15163402615 or
(student_id=1565245896038 or student_name= '渤低犀')))*/
$query1 = new \BaAGee\SimpleDsl\BoolQuery();
$query1->mustMatch('hobby', '打篮球 做美食', 'and');
$query1->mustTerms('work', ['医生', '老师']);
$query2 = new \BaAGee\SimpleDsl\BoolQuery();
$query2->shouldRange('student_id', ['gt' => 1565245896038, 'lte' => 1565364034570]);
$query2->shouldTerm('phone', 15163402615);
$query3 = new \BaAGee\SimpleDsl\BoolQuery();
$query3->shouldTerm('student_id', '1565245896038');
$query3->shouldTerm('student_name', '渤低犀');
$query2->shouldBoolQuery($query3);
$query1->mustBoolQuery($query2);
$query1->notTerm('student_id', 1565252207417);
$dsl = new \BaAGee\SimpleDsl\ConvertDSL($query1);
$dsl->sort(['student_id' => 'asc']);
echo $dsl->getDslJson(true);


$query = new \BaAGee\SimpleDsl\BoolQuery();
$query->mustMultiMatch(['hobby', 'work'], '医生，打篮球');
$dsl = new \BaAGee\SimpleDsl\ConvertDSL();
$dsl->addQuery($query);
$dsl->highlight(['hobby', 'work']);
$dsl->sort(['student_id' => 'desc']);
$dsl->offset(0);
$dsl->limit(100);
echo $dsl->getDslJson(true);


$query = new \BaAGee\SimpleDsl\BoolQuery();
$query->shouldTerms('hobby', ['打篮球', '游戏']);
$query->shouldRange('student_id', ['gt' => 1593022192650]);
$query->shouldLike('student_name', '韧*');
$query->mustMatch('hometown', '济南市历下区和平路');
$query->mustMultiMatch(['hobby', 'work'], '售货员');
$query->shouldMatch('hometown', '济南市历下区和平路');
$query->shouldMultiMatch(['hobby', 'work'], '售货员');
$dsl = new \BaAGee\SimpleDsl\ConvertDSL();
$dsl->addQuery($query);
$dsl->sort(['student_id' => 'asc']);
$dsl->offset(0);
$dsl->highlight(['hometown', 'hobby', 'work']);
$dsl->limit(100);
echo $dsl->getDslJson(true);


$query = new \BaAGee\SimpleDsl\BoolQuery();
$query->notTerms('hobby', ['打篮球', '游戏']);
$query->notRange('student_id', ['gt' => 1593022192650]);
$query->notLike('student_name', '韧*');
$query->shouldMatch('hometown', '济南市历下区和平路');
$query->notMultiMatch(['hobby', 'work'], '售货员');
$query2 = new \BaAGee\SimpleDsl\BoolQuery();
$query2->shouldMatch('hometown', '济南市历下区和平路');
$query2->notMultiMatch(['hobby', 'work'], '售货员');
$query->notBoolQuery($query2);
$dsl = new \BaAGee\SimpleDsl\ConvertDSL();
$dsl->addQuery($query);
$dsl->sort(['student_id' => 'asc']);
$dsl->offset(0);
$dsl->highlight(['hometown', 'hobby', 'work']);
$dsl->limit(100);
echo $dsl->getDslJson(true);
