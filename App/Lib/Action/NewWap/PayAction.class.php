<?php

//充值
class PayAction extends LoadingAction {

    //首页
    public function index() {
        $web_id = $this->website['web_id'];//echo($web_id);获取网站id
        $this->assign("web",$web_id);
        $is_weixin = $this->is_weixin();
        $this->assign("is_weixin",$is_weixin);
        $alance = M('NUserMoney')->where(array('user_id' => $_COOKIE['user_id']))->getField('alance');
        $this->assign("title","账户充值");
        $this->assign("alance", $alance);
        $this->display();
    }

    //充值成功页
    public function ok($readmoney = '', $money = '') {
        $this->title = '账户充值';
        $this->assign('readmoney', $readmoney);
        $this->assign('money', $money);
        $this->display();
    }

    //充值失败页
    public function err() {
        $this->title = '账户充值';
        $this->display();
    }

    //获取订单是否成功
    public function payresult($trade) {
        $state = M('NSystemPay')->where(array('trade' => $trade))->getField('state');
        if ($state == 1) {
            echo 1;
        } else {
            echo 2;
        }
    }

    //支付宝检测
    public function zfbjc($readmoney, $money) {
        $trade = isset($_GET[out_trade_no]) ? $_GET[out_trade_no] : 0;
        $this->assign('trade', $trade);
        $this->assign('chenggong', "http://$_SERVER[HTTP_HOST]/Pay/ok/readmoney/$readmoney/money/$money");
        $this->display();
    }

    //充值订单
    public function pay() {
        $payChannelType = isset($_POST['payChannelType']) ? $_POST['payChannelType'] : 0;
        $moneyType = isset($_POST['moneyType']) ? $_POST['moneyType'] : 0;
        //echo $moneyType;exit();
        $money_array = array(50,365,30,100,200,500);
        if (!isset($money_array[$moneyType])) {
            $this->error("充值必须大于30元");
        }
        $money = $money_array[$moneyType];

        // $money = 0.01;
        //添加未支付订单
        $data = $this->payadd($money, $payChannelType);
        //充值类型
        //当前域名
        $http_host = $_SERVER['HTTP_HOST'];
        switch ($payChannelType) {
            case 0:
                //微信充值
               // echo "http://m.kyueyun.com/NewWap/Payinterface/jsapi/?trade=$data[trade]&money=$money&readmoney=$data[readmoney]&http_host=$http_host";exit();
                header("Location: http://m.kyueyun.com/NewWap/Payinterface/jsapi/?trade=$data[trade]&money=$money&readmoney=$data[readmoney]&http_host=$http_host");
                break;
            case 1:
                //微信扫码
              //  header("Location: http://m.kyueyun.com/NewWap/Payinterface/qrcode/?trade=$data[trade]&money=$money&readmoney=$data[readmoney]&http_host=$http_host");
                break;
            case 2:
                //支付宝充值
                header("Location: http://$http_host/Payinterface/zhifubao/trade/$data[trade]/money/$money/readmoney/$data[readmoney]");
                break;
        }
    }

    //添加钱
    private function payadd($money, $payChannelType) {
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
        if ($money == 365 || $money == 265) {
            $data['is_members'] = 1; //是包年
        }
        //充值类型
        switch ($payChannelType) {
            case 0:
                $data['type'] = "微信充值";
                break;
            case 1:
                $data['type'] = "微信扫码";
                break;
            case 2:
                $data['type'] = "支付宝";
                break;
        }
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

     //充值记录
    public function record() {
        import('ORG.Util.Page');
        $sysch = M('NSystemPay');
        $where['user_id'] = $_COOKIE[user_id];
        $where['state'] = 2;
        $count = $sysch->where($where)->count(); // 查询满足要求的总记录数   
        $Page = new Page($count, 5); // 实例化分页类 传入总记录数和每页显示的记录数  
        $Page->setConfig('theme', " <span class=\"pager_left\">%upPage%&nbsp;</span> <span class=\"pager_conn\">%nowPage%/%totalPage%</span> <span class=\"pager_right\">&nbsp;%downPage%</span>");
        //内容显示
        $jilu = $sysch->where($where)->field('type,money,readmoney,state,time')->limit($Page->firstRow . ',' . $Page->listRows)->order('time desc')->select();
        $this->assign('jilu', $jilu);
        // 分页显示输出
        $show = $Page->show();
        $this->assign('page', $show);
        $this->assign("title","充值记录");
        $this->display();
    }
}
