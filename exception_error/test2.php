<?php
/**
 * Desc:
 * User: baagee()
 * Date: 2018/9/16
 * Time: 上午10:43
 */

include __DIR__ . '/ExceptionErrorHandler.php';
define('ROOT_PATH', __DIR__ . '/');
ExceptionErrorHandler::register(__DIR__ . '/error.log');

echo $aa;