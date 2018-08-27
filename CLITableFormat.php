<?php

/**
 * Desc:
 * User: baagee(LiuhuiDang@sf-express.com)
 * Date: 2018/8/27
 * Time: 下午2:44
 */
class CLITableFormat
{
    protected $column_max_len = [];

    public function __construct()
    {
        if (php_sapi_name() !== PHP_SAPI) {
            die('请在cli模式下运行' . PHP_EOL);
        }
    }

    /**
     * 输出打印
     * @param array $data 要打印的二维数组
     */
    public function print(array $data)
    {
        $this->getColumnMaxLen($data);
        $this->printHeader();
        $this->printData($data);
    }

    // 输出表头
    protected function printHeader()
    {
        // 开始输出表头
        $this->printLine();
        echo '| ';
        foreach ($this->column_max_len as $c => $l) {
            $ll = (mb_strlen($c, 'utf-8') + strlen($c)) / 2;
            $c  = $c . str_repeat(' ', ($l - $ll));
            echo $c . ' | ';
        }
        echo PHP_EOL;
        $this->printLine();
        // 结束输出表头
    }

    // 打印上/下边框
    protected function printLine()
    {
        echo '+';
        foreach ($this->column_max_len as $len) {
            echo str_repeat('-', $len + 2);
            echo '+';
        }
        echo PHP_EOL;
    }

    // 打印数据
    protected function printData(array $data)
    {
        // 开始输出数据
        foreach ($data as $v) {
            echo '| ';
            foreach ($this->column_max_len as $c => $l) {
                $ll    = (mb_strlen($v[$c], 'utf-8') + strlen($v[$c])) / 2;
                $v[$c] = $v[$c] . str_repeat(' ', ($l - $ll));
                echo $v[$c] . ' | ';
            }
            echo PHP_EOL;
        }
        echo '+';
        foreach ($this->column_max_len as $len) {
            echo str_repeat('-', $len + 2);
            echo '+';
        }
        echo PHP_EOL;
        // 结束输出表数据
    }

    protected function getColumnMaxLen(array $data)
    {
        $columns     = array_keys($data[0]);
        $max_col_len = [];
        foreach ($columns as $column) {
            $tmp                  = array_merge(array_column($data, $column), [$column]);
            $len                  = $this->getMaxLength($tmp);//一个单元格最大长度
            $max_col_len[$column] = $len;
        }
        $this->column_max_len = $max_col_len;
    }

    /**
     * 获取数组中最大字符长度
     * @param array $arr
     * @return int
     */
    protected function getMaxLength(array $arr)
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

// 测试
$data = [];
for ($i = 0; $i < 40; $i++) {
    $tmp    = [
        'id'       => $i,
        'name'     => '发发发' . $i,
        'age'      => mt_rand(18, 28),
        'sex'      => '女',
        'money'    => mt_rand(10, 9090),
        'birthday' => date('Y-m-d H:i:s')
    ];
    $data[] = $tmp;
}
$table = new CLITableFormat();
$table->print($data);
/*
+---------------+-----+-----+-------+---------------------+
| name          | age | sex | money | birthday            |
+---------------+-----+-----+-------+---------------------+
| 发发发3700    | 99  | 男  | 60    | 2018-08-27 06:56:04 |
| 发发发97      | 32  | 男  | 58    | 2018-08-27 06:56:04 |
| dssdfbd三国杀 | 40  | 女  | 5759  | 2018-08-27 06:56:04 |
| 发发发578     | 17  | 女  | 6268  | 2018-08-27 06:56:04 |
+---------------+-----+-----+-------+---------------------+
 * */