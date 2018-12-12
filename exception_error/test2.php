<?php
/**
 * Desc:
 * User: baagee()
 * Date: 2018/9/16
 * Time: 上午10:43
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');


//E_NOTICE
//echo $ee;
//Notice: Undefined variable: ee in /Users/baagee/PhpstormProjects/php_code_snippet/exception_error/test2.php on line 25

// E_WARNING
//$a=10;
//echo $a/0;
//PHP Warning:  Division by zero in /Users/baagee/PhpstormProjects/php_code_snippet/exception_error/test2.php on line 13

//E_ERROR
//etst();
/*
 * Fatal error: Uncaught Error: Call to undefined function etst() in /Users/baagee/PhpstormProjects/php_code_snippet/exception_error/test2.php:17
Stack trace:
#0 {main}
  thrown in /Users/baagee/PhpstormProjects/php_code_snippet/exception_error/test2.php on line 17
*/

//E_PARSE
//echo $gfdshd
//PHP Parse error:  syntax error, unexpected end of file, expecting ',' or ';' in /Users/baagee/PhpstormProjects/php_code_snippet/exception_error/test2.php on line 30


function change (&$var) {

    $var += 10;

}

$var = 1;

change(++$var);