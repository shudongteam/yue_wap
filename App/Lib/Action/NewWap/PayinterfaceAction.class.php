<?php

//充值接口
class PayinterfaceAction extends Action {


    //订单 价格 用户 反馈地址 跳转地址        
    public function jsapi($trade, $money, $readmoney, $http_host) {

        $conf = include_once "Payconf/weixin.php";
        if (!$conf) {
            echo "配置单有问题";
            exit();
        }
        vendor('Wpay.JsApiPay');
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

        $input = new WxPayUnifiedOrder();
        $input->SetBody("合作支付"); //商品描述
        $input->SetAttach("无"); //附加信息
        $input->SetOut_trade_no($trade); //订单
        $input->SetTotal_fee($money * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("合作支付"); //商品标记
        $input->SetNotify_url(WxPayConfig::Notify); //回调地址
        $input->SetTrade_type("JSAPI"); //支付类型
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        $this->assign('shibai', "http://$http_host/Pay/err.html"); //失败跳转
        $this->assign('chenggong', "http://$http_host/Pay/ok/". 'readmoney/'.$readmoney.'/money/'.$money); //成功跳转
        $this->assign('trade', $trade); //订单号
        $this->jsApiParameters = $tools->GetJsApiParameters($order);
        $this->display();
    }

    //微信扫码支付
    public function qrcode($trade, $money, $readmoney, $http_host) {
        $conf = include_once "Payconf/weixin.php";
        if (!$conf) {
            echo "配置单有问题";
            exit();
        }
        vendor('Wpay.NativePay');
        $notify = new NativePay();
        $input = new WxPayUnifiedOrder();
        $input->SetBody("合作支付"); //商品描述
        $input->SetAttach("无"); //附加信息
        $input->SetOut_trade_no($trade); //订单
        $input->SetTotal_fee($money * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("合作支付");
        $input->SetNotify_url(WxPayConfig::Notify); //回调地址
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("123456789");
        $result = $notify->GetPayUrl($input);
        // var_dump($result);exit;
        if ($result['return_code'] !== 'SUCCESS') {
            $this->error('生成二维码错误');
        }
        $this->assign('code_url', $result["code_url"]); //二维码
        $this->assign('money', $money); //钱
        $this->assign('payurl', "http://$http_host/Pay/payresult/trade/".$trade); //检测URL
        $this->assign('chenggong', "http://$http_host/Pay/ok/" . 'readmoney/'.$readmoney.'/money/'.$money); //成功跳转
        $this->display();
    }

    //支付宝
    public function zhifubao($trade, $money, $readmoney) {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            //检查手机类型
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            $is_iphone = (strpos($agent, 'iphone')) ? true : false;
            if ($is_iphone) {
                $tishi = "点击手机屏幕右上角的的“<font color=\"red\">┉</font>”菜单按钮,然后选择<font color=\"red\">在Safari中打开</font>就可以进行充值。";
            } else {
                $tishi = "点击手机屏幕右上角的的“<font color=\"red\">┇</font>”菜单按钮,然后选择<font color=\"red\">在浏览器中打开</font>就可以进行充值。";
            }
            $this->assign('tishi', $tishi);
            $this->assign('money', $money);
            $this->display();
        } else {
            echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
            Vendor('Zhifubao.lib.alipay_submit');
            $alipay_config = C('alipay'); //配置方案
            //商户订单号
            $out_trade_no = $trade;
            //订单名称，必填
            $subject = "充值 $money 元";
            //付款金额，必填
            $total_fee = $money;
            //收银台页面上，商品展示的超链接，必填
            // $show_url = $alipay_config['return_url'];
            //商品描述，可空
            $body = "充值 $money 元到账 $readmoney 币";
            $parameter = array(
                "service" => $alipay_config['service'],
                "partner" => $alipay_config['partner'],
                "seller_id" => $alipay_config['seller_id'],
                "payment_type" => $alipay_config['payment_type'],
                "notify_url" => "http://pay.ymzww.cn/Notify/zhifubao",
                "return_url" => "http://$_SERVER[HTTP_HOST]/Pay/zfbjc/readmoney/$readmoney/money/$money",
                "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
                "out_trade_no" => $out_trade_no,
                "subject" => $subject,
                "total_fee" => $total_fee,
                "show_url" => "",
                "app_pay" => "Y", //启用此参数能唤起钱包APP支付宝
                "body" => $body,
            );
            //建立请求
            $alipaySubmit = new AlipaySubmit($alipay_config);
            $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
            echo $html_text;
        }
    }

    //获取检测连接的
    public function jiance($payurl) {
        echo file_get_contents($payurl);
    }

}