<?php

//充值
class PayAction extends GlobalAction {

    //首页
    public function index() {
        $web_id = $this->website['web_id'];//echo($web_id);获取网站id
        $this->assign("web",$web_id);
        $this->assign('user_id', $_COOKIE['user_id']);
        $is_weixin = $this->is_weixin();
        $this->assign("is_weixin",$is_weixin);
        $this->display();
    }

    //充值记录
    public function record() {
        import('ORG.Util.Page');
        $sysch = M('SystemPay');
        $where['user_id'] = $this->to[user_id];
        $where['state'] = 2;
        $count = $sysch->where($where)->count(); // 查询满足要求的总记录数   
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数  
        $Page->setConfig('theme', " <span class=\"pager_left\">%upPage%&nbsp;</span> <span class=\"pager_conn\">%nowPage%/%totalPage%</span> <span class=\"pager_right\">&nbsp;%downPage%</span>");
        //内容显示
        $jilu = $sysch->where($where)->field('type,money,readmoney,state,time')->limit($Page->firstRow . ',' . $Page->listRows)->order('time desc')->select();
        $this->assign('jilu', $jilu);
        $Page->url ='Personal/Pay/record/p/';
        // 分页显示输出
        $show = $Page->show();
        $this->assign('page', $show);

        $this->display();
    }

    //会员包年
    public function package() {
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
        if (is_numeric($_POST[money]) && $_POST[money] >= 1 && $_POST[money] < 10000) {

            $money_array = array(30,50,100,200,500,365, 265);
            if (!in_array($_POST[money], $money_array)) {
                $this->error("充值必须大于30元");
            }
            //添加钱
            $agent_id = isset($_COOKIE['agent']) ? $_COOKIE['agent'] : $this->to[agent_id];
            $data = A('Paycc')->payadd($this->to[user_id], $agent_id, $this->to['integral'], $this->website[web_id]);
            //充值类型
            switch ($_POST[payChannelType]) {
                case 1:
                    //微信充值
                    header("Location: ".WxPayConfig::Submit."/jsapi/?conf=$conf_id&trade=$data[trade]&money=$data[money]");
                    break;
                case 2:
                    //微信扫码
                    if($_COOKIE['user_id'] == 996277){
                        $this->wft($data[trade], $data[money]);
                        exit;
                    }
                    // header("Location: ".WxPayConfig::Submit."/qrcode/?conf=$conf_id&trade=$data[trade]&money=$data[money]");
                    // break;
                case 3:
                    //支付宝充值
                    header("Location: /Personal/Payinterface/zhifubao?trade=$data[trade]&money=$data[money]");
                    break;
            }
        } else {
            $this->error("充值必须大于30元");
        }
    }

    //获取订单是否成功
    public function payresult($trade) {
        $state = M('SystemPay')->where(array('trade' => $trade))->getField('state');
        if ($state == 1) {
            echo 1;
        } else {
            echo 2;
        }
    }


    //微信包年100元抵扣红包
    public function wei_pay() {
        //调用配置单信息
        $conf_id = $this->website[web_id]; //配置单ID
        $conf = include_once "Payconf/$conf_id.php";
        if (!$conf) {
            echo "配置单有问题";
            exit();
        }
        $money = 265;
        // $money = 0.01;
        //添加钱
        $data = A('Paycc')->payadd2($this->to[user_id], $this->to[agent_id], $this->to['integral'], $this->website[web_id]);

        //微信充值
        header("Location: ".WxPayConfig::Submit."/jsapi/?conf=$conf_id&trade=$data[trade]&money=$money");
    }

    //圣诞活动冲12元 送12元 2400阅读币
    public function christmas() {
        //调用配置单信息
        $conf_id = $this->website[web_id]; //配置单ID
        $conf = include_once "Payconf/$conf_id.php";
        if (!$conf) {
            echo "配置单有问题";
            exit();
        }
        $money = 12;
        // $money = 0.01;
        //添加钱
        $data = A('Paycc')->payadd3($this->to[user_id], $this->to[agent_id], $this->to['integral'], $this->website[web_id]);

        //微信充值
        header("Location: ".WxPayConfig::Submit."/jsapi/?conf=$conf_id&trade=$data[trade]&money=$money");
    }

    //威富通-微信扫码支付
    private function wft($trade, $money){
        vendor('Wft.request');
        $req = new Request();
        $res = $req->submitOrderInfo($trade, 0.01);
        // $res = $req->submitOrderInfo($trade, $money);
        $code_img_url = '';
        if (isset($res['code_img_url'])) {
            $code_img_url = $res['code_img_url'];
        }
        $this->assign('money', $money);
        $this->assign('code_img_url', $code_img_url);
        $this->assign('trade', $trade);
        $this->assign('jiance', '/Personal/Pay/payresult/trade/'.$trade);
        $this->assign('chenggong', "/Personal/Payto/ok/trade/".$trade);   
        $this->display('/Pay:wft');
    }
}
