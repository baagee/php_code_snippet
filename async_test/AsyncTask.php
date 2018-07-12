<?php

/**
 * Created by PhpStorm.
 * User: dangliuhui
 * Date: 2018/3/12
 * Time: 下午3:44
 */

class AsyncTask
{
    private static $map_path = __DIR__ . '/task_map_config.php';
    /**
     * 保存任务列表
     * @var array
     */
    protected $map = [];
    /**
     * 储存对象
     * @var null
     */
    private static $obj = null;

    /**
     * 获取对象
     * @return AsyncTask|null
     */
    public static function getInstance()
    {
        if (self::$obj === null) {
            self::$obj = new self();
            if (file_exists(self::$map_path)) {
                $inc = include_once self::$map_path;
                if (is_array($inc)) {
                    self::$obj->map = $inc;
                }
            }
        }
        return self::$obj;
    }

    /**
     *
     */
    private function __clone()
    {
    }

    /**
     * 执行异步任务
     * @param string $task_name 任务名称
     * @param array $data 任务需要的数据
     * @return bool|string
     * @throws Exception
     */
    public function run($task_name, $data = [])
    {
        if ($script_path = $this->getTask($task_name)) {
            return self::taskStart($script_path, $data);
        } else {
            return false;
        }
    }

    /**
     * 获取一个任务
     * @param string $task_name 任务名称
     * @return bool|mixed
     */
    private function getTask($task_name)
    {
        if (array_key_exists($task_name, $this->map)) {
            return $this->map[$task_name];
        } else {
            return false;
        }
    }

    /**
     * 填加一个任务
     * @param string $task_name 任务名
     * @param string $script_path 任务脚本路径
     * @return bool
     */
    public function addTask($task_name, $script_path)
    {
        if (!$this->getTask($task_name)) {
            $this->map[$task_name] = $script_path;
            file_put_contents(self::$map_path, '<?php ' . PHP_EOL . 'return ' . var_export($this->map, true) . ';');
            return true;
        } else {
            return false;
        }
    }

    /**
     * 异步任务开始执行
     * @param string $task_script_path 任务脚本绝对路径
     * @param array $params 脚本参数
     * @return string 条件
     * @throws Exception
     */
    public static function taskStart($task_script_path, $params = [])
    {
        if (file_exists($task_script_path)) {
            $where = '';
            foreach ($params as $k => $v) {
                $where .= ' --' . $k . '=' . $v;
            }
            $command = PHP_BINDIR . '/php ' . $task_script_path . $where . ' &';
            $handle = popen($command, 'w');
            pclose($handle);
            return $where;
        } else {
            throw new \Exception('任务脚本不存在', 100000);
        }
    }
}