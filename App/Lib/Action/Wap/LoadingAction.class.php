<?php
//不加载自动登录功能
class LoadingAction extends Action{

    protected $website;

    public function _initialize() {
        //载入工具包
        $gogju = A('Gongju');
        //判断是什么站点
        $this->website = $gogju->webssite();
        $this->assign('website', $this->website);
    }
}
