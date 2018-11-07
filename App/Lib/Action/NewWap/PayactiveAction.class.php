<?php

//活动充值
class PayactiveAction extends LoadingAction {




    //充值订单
    public function pay() {
        $money = 18;
        $data = $this->payadd($money);
        //微信充值
        $http_host = $_SERVER['HTTP_HOST'];
        header("Location: http://w.ymzww.cn/NewWap/Payinterface/jsapi/?trade=$data[trade]&money=$money&readmoney=$data[readmoney]&http_host=$http_host");

    }

    //添加钱
    private function payadd($money) {
        $user_id = cookie('user_id');
        $shell = cookie('shell');
        if(!$user_id || !$shell){
            $this->error('非法请求');
        }
        $User = M('NUser');
        $where['user_id'] = $user_id;
        $isUser = $User->field('`user_name`,`user_pass`, `agent_id`')->where($where)->find();
        if ($isUser) {
            $shell2 = md5($isUser['user_name'] . $isUser['user_pass'] . C('ALL_ps'));
            if ($shell != $shell2) {
                $this->error('非法请求2');
            }
        }

        $data['user_id']  = $user_id;
        $data['web_id']   = $this->website['web_id'];
        //代理
        if (isset($_COOKIE['agent'])) {
            $data['agent_id'] = $_COOKIE['agent'];
        } else {
            $data['agent_id'] = $isUser['agent_id'];
        }
        //渠道号
        if ($_COOKIE['channel']) {
            $data['channel_id'] = $_COOKIE['channel'];
            $datas['channel_id'] = $_COOKIE['channel'];
        } else {
            $data['channel_id'] = 0;
        }
        //是否包年
        $data['is_members'] = 0; 
        //充值类型
        $data['type'] = "微信充值(新人充18送18)";

        //生成订单
        $b = date('YmdHis');
        //生成随机订单号
        $data['trade'] = $b . mt_rand(1000000, 9999999);
        $data['money'] = $money;
        $data['readmoney'] = $money * 100 + $this->duosong($money) + $this->vip($money);
        $data['state'] = 1;
        $data['time'] = date('Y-m-d H:i:s', time());
        $isok = M('NSystemPay')->add($data);
        $datas['pay_total']=  array('exp', "pay_total+$money");
        if($data['channel']!=0){
            M('NAgentChannel')->save($datas);
        }
        
        if ($isok) {
            return $data; //数据处理进行数组返回
        } else {
            var_dump($data);exit;
//            $this->error("系统错误");
        }
    }

    //vip等级
    private function vip($money) {
        //查询用户VIP等级
        $integral = M('NUserMoney')->where('user_id ='.$_COOKIE['user_id'])->getField('money');
        $level = A('NewVipLevel')->viplevel($integral);
        switch ($level) {
            case 2:
                $ticheng = $money * 1;
                break;
            case 3:
                $ticheng = $money * 2;
                break;
            case 4:
                $ticheng = $money * 3;
                break;
            case 5:
                $ticheng = $money * 4;
                break;
            case 6:
                $ticheng = $money * 5;
                break;
            default:
                $ticheng = 0;
        }
        return $ticheng;
    }

    //多送阅读币            
    private function duosong($money) {
        if ($money >= 500) {
            return 5800;
        } elseif ($money >= 200) {
            return 2200;
        } elseif ($money >= 100) {
            return 1000;
        } elseif ($money >= 50) {
            return 400;
        } else {
            return 0;
        }
    }
}
