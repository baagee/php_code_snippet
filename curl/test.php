<?php
/**
 * Desc:
 * User: baagee
 * Date: 2019/10/4
 * Time: 17:48
 */

use CurlRequest\MultipleRequest;
use CurlRequest\SingleRequest;

include_once './CurlOptions.php';
include_once "./CurlRequest.php";
include_once "./SingleRequest.php";
include_once "./MultipleRequest.php";

function testSingle()
{
    $config  = [];
    $request = new SingleRequest($config);

    $path    = 'http://10.188.60.200:8550/api/test/curl';
    $params  = [
        'username' => 'ghfjfhj',
        'password' => 'fhdghdf',
        'code'     => 'dsgsgd',
    ];
    $method  = 'GET';
    $headers = [];
    $res     = $request->request($path, $params, $method, $headers);

    var_dump($res);
}


function testMulti()
{
    $config   = [
        'host'          => '10.188.60.200:8550',
        'return_header' => 0
    ];
    $mRequest = new MultipleRequest($config);

    $path   = 'http://10.188.60.200:8550/api/test/curl';
    $params = [
        'username' => 'ghfjfhj',
        'password' => 'fhdghdf',
        'code'     => 'dsgsgd',
    ];

    $data = [
        [
            'path'    => $path,
            'params'  => $params,
            'method'  => 'POST',
            'headers' => [],
            'cookies' => ''
        ],
        [
            'path'    => $path,
            'params'  => $params,
            'method'  => 'GET',
            'headers' => [],
            'cookies' => ''
        ],
        [
            'path'    => $path,
            'params'  => $params,
            'method'  => 'DELETE',
            'headers' => [],
            'cookies' => ''
        ],
        [
            'path'    => $path,
            'params'  => $params,
            'method'  => 'PUT',
            'headers' => [],
            'cookies' => ''
        ],
        [
            'path'    => $path,
            'params'  => $params,
            'method'  => 'OPTIONS',
            'headers' => [],
            'cookies' => ''
        ],
    ];
    $t    = microtime(true);
    $res  = $mRequest->request($data);
    $res  = $mRequest->request($data);
    file_put_contents('./res.json', json_encode($res));
    var_dump(microtime(true) - $t);
}

testMulti();
testSingle();



