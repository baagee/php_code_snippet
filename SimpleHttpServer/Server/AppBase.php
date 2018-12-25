<?php
/**
 * Desc: 抽象app类
 * User: baagee
 * Date: 2018/12/25
 * Time: 下午7:35
 */

namespace SimServer;
abstract class AppBase
{
    protected $request = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    abstract public function run();
}