<?php
/**
 * Desc: 自定义基础app父类
 * User: baagee
 * Date: 2018/12/25
 * Time: 上午11:02
 */

namespace App;

use SimServer\AppBase;
use SimServer\Request;
use SimServer\Response;

class App extends AppBase
{
    protected $controller = '';
    protected $action     = '';

    public function __construct(Request $request)
    {
        parent::__construct($request);
        // /module/controller/action  or /controller/action
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
        $controller = 'App\\Controller\\' . ucfirst($this->controller);
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