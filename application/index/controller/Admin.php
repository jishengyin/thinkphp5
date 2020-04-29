<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Request;
use app\index\model\Admin as AdminModel;
use think\Session;
use think\Cache;


class Admin extends Base
{
    //登录
    public function login()
    {
        return $this-> view ->fetch();
    }

    //验证登录
    public function checkLogin(Request $request)
    {
        $status = 0;
        $result = '';
        $data = $request->param();

        //创建验证规则
        $rule = [
            'name|用户名' => 'require',
            'password|密码' => 'require',
            'verify|验证码' => 'require|captcha',
        ];
        //自定义验证失败的提示信息
        $msg = [
            'name' => ['require'=>'用户名不能为空，请检查'],
            'password' => ['require'=>'密码不能为空，请检查'],
            'verify'=>[
                'require'=> '验证码不能为空',
                'captcha'=> '验证码错误',
            ],
        ];
        //进行验证
        $result = $this->validate($data ,$rule ,$msg);

        //如果验证成功则执行
        if($result === true){
            //构造查询条件
            $map = [
                'name' => $data['name'],
                'password' => md5($data['password']),
            ];
            //查询用户系信息
            $Admin = AdminModel::get($map);
            if($Admin == null){
                $result = '没有找到该用户';
            }else{
                $status = 1;
                $result = '登录成功';

                //更新登录次数
                $Admin->setInc('login_count');
                //设置用户信息
                Session::set('admin_id', $Admin->id);//用户id
                Session::set('admin_info', $Admin->getData());//用户信息
                //设置登录缓存用于判断唯一登录
                Cache::set($Admin->id,session_id());

            }
        }
        return ['status'=> $status , 'message' => $result , 'data' => $data];
    }

    //登出
    public function logout()
    {
        $Admin_id = Session::get('Admin_id');
        //退出前先更新登录时间字段,下次登录时就知道上次登录时间了
        AdminModel::update(['login_time'=>time()],['id'=> Session::get('admin_id')]);
        //注销session
        Session::delete('admin_id');
        Session::delete('admin_info');
        //删除缓存
        Cache::rm($Admin_id);
        $this->success('注销登录，正在返回','Admin/login');
    }

    public function  adminList()
    {
        $this -> view ->assign('title' ,'管理员列表');
        $this -> view ->assign('keywords' ,'后台管理系统');
        $this -> view ->assign('desc' ,'desc');

        $this -> view ->count = AdminModel::count();
        //判断是不是admin
        $userName = Session::get('admin_info.name');
        if($userName == 'admin'){
            //所有记录
            $list = AdminModel::all();
        }else{
            $list = AdminModel::all(['name'=>$userName]);
        }

        $this -> view -> assign('list' , $list);

        return $this->view ->fetch('admin_list');
    }
}
