<?php

//活动充值 界面
class PayactiveAction extends GlobalAction {

    //首页
    public function index() {
        $web_id = $this->website['web_id'];//echo($web_id);获取网站id
        $this->assign("web",$web_id);
        $this->assign('user_id', $_COOKIE['user_id']);
        $is_weixin = $this->is_weixin();
        $this->assign("is_weixin",$is_weixin);
        $this->display();
    }

  

    //充值订单
    public function pay() {
        //调用配置单信息
        $conf_id = $this->website[web_id]; //配置单ID
        $conf = include_once "Payconf/$conf_id.php";
        if (!$conf) {
            echo "配置单有问题";
            exit();
        }

        $money = isset($_POST['money']) ? intval($_POST['money']) : 0;
        $money_array = array(18,68);
        if (!in_array($money, $money_array)) {
            $this->error("充值必须大于18元");
        }
        //添加钱
        $agent_id = isset($_COOKIE['agent']) ? $_COOKIE['agent'] : $this->to[agent_id];
        $data = $this->create_trade($this->to[user_id], $agent_id, $this->website[web_id], $money);
        //充值类型
        switch ($_POST[payChannelType]) {
            case 1:
                //微信充值
                header("Location: ".WxPayConfig::Submit."/jsapi/?conf=$conf_id&trade=$data[trade]&money=$data[money]");
                break;
            case 3:
                //支付宝充值
                header("Location: /Personal/Payinterface/zhifubao?trade=$data[trade]&money=$data[money]");
                break;
        }
    }


    //添加订单
    private function create_trade($user_id, $agent_id, $web_id, $money) {
        $data['user_id'] = $user_id;
        $data['agent_id'] = $agent_id;
        $data['web_id'] = $web_id;
        //渠道号
        if ($_COOKIE[channel]) {
            $data['channel_id'] = $_COOKIE[channel];
        } else {
            $data['channel_id'] = 0;
        }
        $data['is_members'] = 0; 

        //充值类型
        $data['type'] = "春节充值活动";

        //生成订单
        $b = date('YmdHis');

        //生成随机订单号
        $data['trade'] = $b . mt_rand(1000000, 9999999);
        $data['money'] = $money;
        $data['readmoney'] = $money * 200;
        $data['state'] = 1;
        $data['time'] = date('Y-m-d H:i:s', time());
        $isok = M('SystemPay')->add($data);
        if ($isok) {
            return $data; //数据处理进行数组返回
        } else {
            $this->error("系统错误");
        }
    }
}
