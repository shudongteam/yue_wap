<?php

//各类充值提示
class PaytoAction extends Action {

    //微信成功后
    public function ok($trade) {
        $issc = M('SystemPay')->where(array('trade' => $trade))->field('readmoney,is_members')->find();
        $this->assign('readmoney', $issc[readmoney]);
        $this->assign('is_members', $issc[is_members]);
        $this->display();
    }

    //支付宝成功
    public function zhifubaook() {
        $trade = $_GET[out_trade_no];
        $issc = M('SystemPay')->where(array('trade' => $trade))->field('readmoney')->find();
        $this->assign('readmoney', $issc[readmoney]);
        $this->display("ok");
    }

    //支付宝提示
    public function zhifubao($trade) {
        $systempay = M('SystemPay');
        $issc = $systempay->where(array('trade' => $trade, 'state' => 1))->find();
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            if ($issc) {
                //检查手机类型
                $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
                $is_iphone = (strpos($agent, 'iphone')) ? true : false;
                if ($is_iphone) {
                    $issc['tishi'] = "<b>方法一:</b>点击手机屏幕右上角的的“<font color=\"red\">┉</font>”菜单按钮,然后选择<font color=\"red\">在Safari中打开</font>就可以进行充值。</span>";
                } else {
                    $issc['tishi'] = "<b>方法一:</b>点击手机屏幕右上角的的“<font color=\"red\">┇</font>”菜单按钮,然后选择<font color=\"red\">在浏览器中打开</font>就可以进行充值。</span>";
                }
                $this->assign('issc', $issc);
                $this->display();
            } else {
                $this->error("订单已经被处理");
            }
        } else {
            header("Location: /Personal/Payinterface/zhifubao?trade=$issc[trade]&money=$issc[money]");
        }
    }

}
