<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/25
 * Time: 上午11:02
 */

namespace App;

use SimServer\Request;
use SimServer\Response;

class App
{
    protected $controller = '';
    protected $action     = '';

    protected $request = null;

    public function __construct(Request $request)
    {
        $tmp = explode('/', trim($request->path, '/'));
        if (count($tmp) < 2) {
            Response::setStatusCode(404);
        }
        $this->controller = $tmp[0] ?? '';
        $this->action     = $tmp[1] ?? '';
        $this->request    = $request;
    }

    public function run()
    {
        $controller = 'App\\Controller\\' . $this->controller;
        if (class_exists($controller)) {
            $controller = new $controller();
            $action     = $this->action;
            if (method_exists($controller, $action)) {
                return $controller->$action($this->request);
            }
        }
        Response::setStatusCode(404);
        return '';
    }
}