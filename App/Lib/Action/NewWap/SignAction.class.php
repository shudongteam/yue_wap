<?php

//签到送礼
class SignAction extends GlobalAction {

    //每日签到
    public function index() {
        $this->title = '每日签到';
        $this->display();
    }

    //首冲
    public function first() {
        $this->title = '首冲礼包';
        $this->display();
    }    


    //微信
    public function weixin() {
        $this->title = '微信礼包';
        $this->display();
    }

    //每日签到随机加1-50个阅读币
    public function add() {
        $user = M('NUserMoney');
        $where['user_id'] = cookie('user_id');
        $myuser = $user->where($where)->field('sign_num,sign_time')->find();
        if ($myuser[sign_time] == date("Y-m-d", time())) {
            echo "今天您已经签到过了！"; 
            die();
        }
        $alance = mt_rand(30,50);
        $data['alance'] = array('exp', "alance+{$alance}"); 
        $data['sign_num'] = array('exp', "sign_num+1");;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "恭喜获得{$alance}个阅读币";
    }


    //首冲礼包:领取500阅读币
    public function first_add() {

        $user = M('NUserMoney');
        $res = $user->field("money")->find(cookie('user_id'));
        if (isset($res['money'])) {
            if ($res['money'] <= 0) {
            echo "您还没有充值！"; 
            die();
            }
        } else {
            echo "您还没有充值！"; 
            die();
        }

        $gift = M('NSystemGift');
        $where['type'] = 1;//首冲
        $where['user_id'] = cookie('user_id');
        $res = $gift->where($where)->find(); 
        if ($res) {
            echo "您已经领取过首冲礼包！"; 
            die();
        } 

        $where2['user_id'] = cookie('user_id');
        $data['alance'] = array('exp', "alance+500");
        $isok = $user->where($where2)->save($data);
        if ($isok) {
            $arr['type'] = 1;//首冲
            $arr['time'] = date('Y-m-d H:i:s', time());
            $arr['user_id'] =  cookie('user_id');
            $gift->add($arr);
            echo "领取成功!";
        } else {
            echo "领取失败!";
        }
    }

    //微信礼包:领取100阅读币
    public function weixin_add() {
        $gift = M('NSystemGift');
        $where['type'] = 2;//微信
        $where['user_id'] = cookie('user_id');
        $res = $gift->where($where)->find();
        if ($res) {
            echo "您已经领取过微信礼包！"; 
            die();
        } 
        $user = M('NUserMoney');
        $where2['user_id'] = cookie('user_id');
        $data['alance'] = array('exp', "alance+100");
        $isok = $user->where($where2)->save($data);
        if ($isok) {
            $arr['type'] = 2;//微信
            $arr['time'] = date('Y-m-d H:i:s', time());
            $arr['user_id'] = cookie('user_id');
            $gift->add($arr);
            echo "领取成功!";
        } else {
            echo "领取失败!";
        }
    }
}
