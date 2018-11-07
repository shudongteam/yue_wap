<?php

//签到送礼
class SignAction extends GlobalAction {

    //每日签到
    public function index() {
        $this->display();
    }

    //首冲
    public function first() {
        $this->display();
    }    


    //微信
    public function weixin() {
        $this->display();
    }

    //每日签到随机加1-50个阅读币
    public function add() {
        if (!$this->is_sign()) {
            echo "今天您已经签到过了！"; 
            die();
        } 
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $alance = mt_rand(30,50);
        $data['alance'] = array('exp', "alance+{$alance}"); 
        //$data['integral'] = array('exp', "integral+5"); 
        $data['sign_num'] = array('exp', "sign_num+1");;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "恭喜获得{$alance}个阅读币";
    }


    //首冲礼包:领取500阅读币
    public function first_add() {
        if (!$this->is_first()) {
            echo "您还没有充值！"; 
            die();
        } 
        if (!$this->is_get()) {
            echo "您已经领取过首冲礼包！"; 
            die();
        } 
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $data['alance'] = array('exp', "alance+500");
        $isok = $user->where($where)->save($data);
        if ($isok) {
            //type 1购买2打赏3抽奖,  100????
             //添加记录   type 100
            // $user_consumerecord=M('UserConsumerecord');
            // $data['user_id']= $_COOKIE[user_id];
            // $data['type']=100;
            // $data['money']=0;
            // $data['title']='您获得以下奖品：2张推荐票，2颗阅明珠。1次抽奖机会，100阅明币。';
            // $data['dosomething']='首冲礼包';
            // $user_consumerecord->add($data);
            
            $gift = M('SystemGift');
            $arr['type'] = 1;//首冲
            $arr['time'] = date('Y-m-d H:i:s', time());
            $arr['user_id'] =  $this->to[user_id];
            $gift->add($arr);
            echo "领取成功!";
        } else {
            echo "领取失败!";
        }
    }

    //微信礼包:领取100阅读币
    public function weixin_add() {
        if (!$this->is_weixin()) {
            echo "您已经领取过微信礼包！"; 
            die();
        } 
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $data['alance'] = array('exp', "alance+100");
        $isok = $user->where($where)->save($data);
        if ($isok) {
            $gift = M('SystemGift');
            $arr['type'] = 2;//微信
            $arr['time'] = date('Y-m-d H:i:s', time());
            $arr['user_id'] = $this->to[user_id];
            $gift->add($arr);
            echo "领取成功!";
        } else {
            echo "领取失败!";
        }
    }

    //是否签到
    public function is_sign(){
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $myuser = $user->where($where)->field('sign_num,sign_time')->find();
        if ($myuser[sign_time] == date("Y-m-d", time())) {
            return false;
        }
        return true;
    }

    //是否领取
    public function is_get(){
        $gift = M('SystemGift');
        $where['type'] = 1;//首冲
        $where['user_id'] = $this->to[user_id];
        $res = $gift->where($where)->find();
        if ($res) {
            return false;
        }
        return true;
    }

    //是否首冲
    public function is_first(){
        $user = M('User');
        $res = $user->field("money")->find($this->to[user_id]);
        if (isset($res['money'])) {
            if ($res['money'] <= 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    //是否微信端领取
    public function is_weixin(){
        $gift = M('SystemGift');
        $where['type'] = 2;//微信
        $where['user_id'] = $this->to[user_id];
        $res = $gift->where($where)->find();
        if ($res) {
            return false;
        }
        return true;
    }



/*    //执行初始化赠送礼物方式
    private function giftyi() {
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $data['alance'] = array('exp', "alance+5"); //送5阅读币
        $data['integral'] = array('exp', "integral+5"); //送5积分
        $data['sign_num'] = 1;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "得到5个阅读币5个积分";
    }

    private function gifter() {
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $data['vipvote'] = array('exp', "vipvote+1"); //送阅明珠
        $data['integral'] = array('exp', "integral+5"); //送5积分
        $data['sign_num'] = 2;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "得到1个红钻5个积分";
    }

    private function giftsan() {
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $data['vote'] = array('exp', "vote+3"); //推荐票
        $data['integral'] = array('exp', "integral+5"); //送5积分
        $data['sign_num'] = 3;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "得到3张推荐票5个积分";
    }

    private function giftsi() {
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $data['vipvote'] = array('exp', "vipvote+3"); //送阅明珠
        $data['integral'] = array('exp', "integral+10"); //送5积分
        $data['sign_num'] = 4;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "得到3个红钻10个积分";
    }

    private function giftwu() {
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $data['alance'] = array('exp', "alance+10"); //送阅读币
        $data['integral'] = array('exp', "integral+5"); //送5积分
        $data['sign_num'] = 5;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "得到10个阅读币5个积分";
    }

    private function giftliu() {
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
         $data['alance'] = array('exp', "alance+20"); //送阅读币
        $data['integral'] = array('exp', "integral+5"); //送5积分
        $data['sign_num'] = 6;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "得到20个阅读币5个积分";
    }

    private function giftqi() {
        $user = M('User');
        $where['user_id'] = $this->to[user_id];
        $data['alance'] = array('exp', "alance+100"); //送20
        $data['integral'] = array('exp', "integral+10"); //送10积分
        $data['sign_num'] = 7;
        $data['sign_time'] = date("Y-m-d");
        $user->where($where)->save($data);
        echo "得到100个阅明币10个积分";
    }*/

}
