<?php
/**
 * Desc: php类和文件自动加载
 * User: baagee
 * Date: 2018/10/23
 * Time: 下午6:14
 */

class AutoLoader
{
    // 保存顶级命名空间
    protected $topSpaceMap = [];
    // 自动引入的没有命名空间的PHP文件
    protected $files = [];
    // 类和文件路径的映射
    protected $classMap = [];
    // 缓存classMap的文件
    protected $cacheClassMapFile = '';

    // 是否缓存类映射
    protected $isCache = false;

    /**
     * 引入没有命名空间的PHP文件
     * @throws Exception
     */
    public function loadFiles()
    {
        foreach ($this->files as $file) {
            $fullPath = realpath($file);
            if (is_file($fullPath)) {
                include_once $fullPath;
            } else {
                throw new Exception($fullPath . ' Not a file');
            }
        }
    }

    /**
     * AutoLoader constructor.
     * @param array  $autoload          自动引入的php文件配置
     * @param string $cacheClassMapFile 缓存文件
     * @throws Exception
     */
    public function __construct(array $autoload, $cacheClassMapFile = '')
    {
        $this->files             = $autoload['file'];
        $this->topSpaceMap       = $autoload['namespace'];
        $this->cacheClassMapFile = $cacheClassMapFile;

        if (!empty($this->cacheClassMapFile)) {
            $this->isCache = true;
            if (is_file($this->cacheClassMapFile)) {
                $inc = include_once $this->cacheClassMapFile;
                if (is_array($inc)) {
                    $this->classMap = $inc;
                }
            }
        }

        $this->loadFiles();
    }

    /**
     * 自动加载器
     * @param $className
     * @return mixed
     * @throws Exception
     */
    public function autoload($className)
    {
        if ($this->isCache && !empty($this->classMap) && array_key_exists($className, $this->classMap)) {
            // 直接取缓存
            $fullClassPath = $this->classMap[$className];
        } else {
            // 查找文件
            $fullClassPath = self::findFile($className);
        }
        $this->classMap[$className] = $fullClassPath;// 放到属性数组里，以后直接用
        // 引入文件
        include_once $fullClassPath;
    }

    /**
     * 对象销毁之前缓存起来
     */
    public function __destruct()
    {
        if ($this->isCache) {
            // todo 解决：如果开启缓存，每次运行完都会写入文件，即使没有引入新的类，增加IO操作
            // 思路：判断销毁之前的classMap和初始的classMap是否有增加决定是否写入
            $this->writeCache();// 缓存
        }
    }

    /**
     * 缓存
     */
    private function writeCache()
    {
        if (!empty($this->cacheClassMapFile)) {
            file_put_contents($this->cacheClassMapFile, '<?php ' . PHP_EOL . '// Create time: '
                . date('Y-m-d H:i:s') . PHP_EOL . 'return ' . var_export($this->classMap, true) . ';');
        }
    }

    /**
     * 解析文件路径
     * @param string $className 类
     * @return string 类的路径
     * @throws Exception
     */
    private function findFile($className)
    {
        $class_arr = explode('\\', $className);
        $topSpace  = $class_arr[0];
        if (!array_key_exists($topSpace, $this->topSpaceMap)) {
            throw new Exception('Class [' . $className . '] does not exist');
        } else {
            //转换路径
            unset($class_arr[0]);
            $rightPath     = implode(DIRECTORY_SEPARATOR, $class_arr);
            $leftPath      = rtrim($this->topSpaceMap[$topSpace], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $fullClassPath = realpath($leftPath . $rightPath . '.php');//绝对路径
            if (!is_file($fullClassPath)) {
                throw new \Exception($fullClassPath . ' Not a file');
            }
            return $fullClassPath;
        }
    }
}

function autoload()
{
    $autoload_conf = json_decode(file_get_contents('./autoload.json'), true);
    if (!is_array($autoload_conf)) {
        die('配置文件autoload.json格式不正确');
    }
    spl_autoload_register([new AutoLoader($autoload_conf['autoload'], $autoload_conf['cacheFile']), 'autoload']);
}

$isCache = true;

autoload();