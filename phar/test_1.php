<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/10/20
 * Time: 上午10:58
 */


$phar = new Phar('test_1.phar', 0, 'test_1.phar');
// 添加project里面的所有文件到yunke.phar归档文件
$phar->buildFromDirectory(dirname(__FILE__) . '/project');
$phar->setStub('GIF89a' . '<?php __HALT_COMPILER();?>');
//设置执行时的入口文件，第一个用于命令行，第二个用于浏览器访问，这里都设置为index.php
$phar->compressFiles(Phar::GZ);
//$phar->stopBuffering();
$phar->setDefaultStub('index.php');

// $phar->addFromString('database/password','sdgsdf');
rename('test_1.phar', 'test_1');
