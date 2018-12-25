<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/25
 * Time: 上午11:06
 */

namespace App\Controller;

use SimServer\Request;
use SimServer\Response;

class User
{
    // 返回json测试
    public function json(Request $request)
    {
        Response::setHeader('Content-Type', 'application/json; charset=utf-8');
        Response::setCookie('time', date('Y-m-d H:i:s'));
        return json_encode($request);
    }

    // 500测试
    public function fiveoo(Request $request)
    {
        throw new \Exception('500 test');
    }

    // 返回xml测试
    public function xml(Request $request)
    {
        $get = $request->get_params;
        $xml = "<?xml version='1.0' encoding='UTF-8'?>";
        $xml .= "<root>";
        $xml .= self::array2XML($get);
        $xml .= "</root>";
        Response::setHeader('Content-Type', 'application/xml; charset=utf-8');
        return $xml;
    }

    // 返回html测试
    public function html(Request $request)
    {
        $post = $request->post_params;
        $html = '<h1>接收到的post数据' . json_encode($post, JSON_UNESCAPED_UNICODE) . '</h1>';
        Response::setHeader('Content-Type', 'text/html; charset=utf-8');
        return $html;
    }

    private static function array2XML($data)
    {
        $xml = $attr = '';
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $attr = " id='{$key}'";
                $key  = "item";
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= is_array($value) ? self::array2XML($value) : $value;
            $xml .= "</{$key}>";
        }
        return $xml;
    }
}