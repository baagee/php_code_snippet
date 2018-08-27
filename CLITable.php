<?php

/**
 * Desc: 命令行二维数组输出表格
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/8/24
 * Time: 下午6:48
 */
class CLITable
{
    public static function draw(array $data)
    {
        $columns = array_keys($data[0]);
        $max_col_len = [];
        foreach ($columns as $column) {
            $tmp = array_merge(array_column($data, $column), [$column]);
            $len = self::getMaxLength($tmp);//最大长度
            $max_col_len[$column] = $len;
        }
        foreach ($max_col_len as $c => $l) {
            $ll = (mb_strlen($c, 'utf-8') + strlen($c)) / 2;
            $c = $c . str_repeat(' ', ($l - $ll));
            echo $c . ' | ';
        }
        echo PHP_EOL;
        foreach ($data as $v) {
            foreach ($max_col_len as $c => $l) {
                $ll = (mb_strlen($v[$c], 'utf-8') + strlen($v[$c])) / 2;
                $v[$c] = $v[$c] . str_repeat(' ', ($l - $ll));
                echo $v[$c] . ' | ';
            }
            echo PHP_EOL;
        }
    }

    /**
     * 获取数组中最大字符长度
     * @param array $arr
     * @return int
     */
    protected static function getMaxLength(array $arr)
    {
        $index = 0;
        foreach ($arr as $k => $i) {
            $a = (mb_strlen($i, 'utf-8') + strlen($i)) / 2;
            $b = (mb_strlen($arr[$index], 'utf-8') + strlen($arr[$index])) / 2;
            if ($a > $b) {
                $index = $k;
            }
        }
        return (mb_strlen($arr[$index], 'utf-8') + strlen($arr[$index])) / 2;
    }
}
//
$data = [
    [
        'name' => '发发发3' . mt_rand(10, 999),
        'age' => mt_rand(10, 99),
        'sex' => '男',
        'money' => mt_rand(10, 99),
        'birthday' => date('Y-m-d H:i:s')
    ],
    [
        'name' => '发发发' . mt_rand(10, 99),
        'age' => mt_rand(10, 99),
        'sex' => '男',
        'money' => mt_rand(10, 99),
        'birthday' => date('Y-m-d H:i:s')
    ],
    [
        'name' => '发发发' . mt_rand(100, 999),
        'age' => mt_rand(10, 99),
        'sex' => '女',
        'money' => mt_rand(10, 9090),
        'birthday' => date('Y-m-d H:i:s')
    ],
];
CLITable::draw($data);
/*
SFdeMacBook-Pro-15:php_code_snippet baagee$ php CLITable.php
name     | age | sex | money | birthday            |
发发发373 | 24  | 男  | 37    | 2018-08-27 05:22:06 |
发发发18  | 89  | 男  | 86    | 2018-08-27 05:22:06 |
发发发345 | 80  | 女  | 1700  | 2018-08-27 05:22:06 |
 */