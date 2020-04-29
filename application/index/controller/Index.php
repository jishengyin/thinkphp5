<?php
namespace app\index\controller;

class Index extends Base
{
    public function index()
    {
        //判断是否登录
        $this->isLogin();

        return $this-> view ->fetch();
    }
}
