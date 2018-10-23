<?php
$phar = new Phar('./composer.phar');
$a    = $phar['/src/bootstrap.php']->getContent();
var_dump($a);
$file=date('Y-m-d_H-i-s');
$b = $phar->addFromString('/' . $file . '.json', time());
var_dump($phar[$file.'.json']->getContent());