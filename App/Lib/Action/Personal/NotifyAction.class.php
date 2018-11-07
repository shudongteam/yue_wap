<?php

//支付反馈接收接口
class NotifyAction extends Action {

    //微信支付反馈            
    public function weixin() {
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notifyinfo = $this->simplest_xml_to_array($this->uncdata($xml));
        $result_code = $notifyinfo['result_code']; //返回状态码
        $out_trade_no = $notifyinfo['out_trade_no']; //商户订单号
        $transaction_id = $notifyinfo['transaction_id']; //支付订单
        $money          = $notifyinfo['total_fee']/100; //订单总金额，单位为分
        if ($result_code == 'SUCCESS') {
            //反馈到服务器
            $num = 1;
            while ($num <= 3) {
                file_put_contents('zhang.txt', "执行$num 次", FILE_APPEND);
                $jieguo = $this->chuli($out_trade_no, $transaction_id, $money); //调用处理方法    
                if ($jieguo == 1) {
                    break;
                }
                $num++;
            }
            //发消息
        }
        $this->weixinxml();
        file_put_contents('zhang.txt', "通知" . date('Y-m-d H:i:s', time()), FILE_APPEND);
    }

    //微信返回码
    public function weixinxml() {
        echo "<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
    }

    //支付宝相应
    public function zhifubao() {
        Vendor('Zhifubao.lib.alipay_notify');
        $alipay_config = C('alipay'); //配置方案
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {
            //商户订单号
            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            $money = intval($_POST['total_fee']);
            //反馈到服务器
            $num = 1;
            while ($num <= 3) {
                file_put_contents('zhang.txt', "执行$num 次", FILE_APPEND);
                $jieguo = $this->chuli($out_trade_no, $trade_no, $money); //调用处理方法    
                if ($jieguo == 1) {
                    break;
                }
                $num++;
            }
            echo "success";
        } else {
            echo "fail";
        }
    }

    //结果处理/订单号/交易号
    private function chuli($mhtOrderNo, $mhtOrderName, $money) {
        //处理订单
        $systempay = M('SystemPay');
        $issc = $systempay->where(array('trade' => $mhtOrderNo, 'state' => 1))->find();
        if (is_array($issc)) {
            //判断钱是否正确
            if ($issc['money'] != $money) {
                file_put_contents('qian.txt', "user_id:$issc[user_id],truemoney:$issc[money],orimoney:$money,trade:$trade--time:".date('Y-m-d H:i:s', time()), FILE_APPEND);
                exit('钱不对!');
            }         
            $systempay->startTrans(); //开启事物
            $isok = $systempay->where(array('id' => $issc[id]))->save(array('transaction' => $mhtOrderName, 'state' => 2));
            //包年还是充值
            if ($issc[is_members] == 1) {
                $oks = $this->baoyue($issc);
            } else {
                $oks = $this->jiaqian($issc);
            }
            if ($issc[channel_id]) {
                $data['pay_total'] = array('exp', "pay_total+$issc[money]"); //总充值
                $data['money_num'] = array('exp', "money_num+1"); //总充笔数
                M('AgentChannel')->where(array('channel_id' => $issc[channel_id]))->save($data);
            }
            if ($isok && $oks) {
                $systempay->commit(); //成功则提交  
                return 1;
            } else {
                $systempay->rollback(); //不成功，则回滚
                return 2; //不对   
            }
        } else {
            return 2; //不对    
        }
    }

    //给用户加钱
    private function jiaqian($issc) {
        $user = M('User');
        $data['money'] = array('exp', "money+$issc[money]"); //总充值
        $data['alance'] = array('exp', "alance+$issc[readmoney]"); //余额
        $oks = $user->where(array('user_id' => $issc[user_id]))->save($data);
        return $oks;
    }

    //给用户包月             
    private function baoyue($issc) {
        $zhanghu = M('User')->where(array('user_id' => $issc[user_id]))->field('vip_time')->find();
        if (strtotime($zhanghu[vip_time]) > strtotime(date('Y-m-d H:i:s', time()))) {
            $newtime = date('Y-m-d H:i:s', strtotime($zhanghu[vip_time] . "+365 day"));
            $oks = M('User')->where(array('user_id' => $issc[user_id]))->save(array('vip_time' => $newtime, 'money' => array('exp', "money+$issc[money]")));
            return $oks;
        } else {
            $viptime = date("Y-m-d H:i:s");
            $newtime = date('Y-m-d H:i:s', strtotime($viptime . "+365 day"));
            $oks = M('User')->where(array('user_id' => $issc[user_id]))->save(array('vip_time' => $newtime, 'money' => array('exp', "money+$issc[money]")));
            return $oks;
        }
    }

    public function uncdata($xml) {
        $state = 'out';
        $a = str_split($xml);
        $new_xml = '';
        foreach ($a AS $k => $v) {
            // Dealwith "state".
            switch ($state) {
                case'out':
                    if ('<' == $v) {
                        $state = $v;
                    } else {
                        $new_xml .= $v;
                    }
                    break;

                case'<':
                    if ('!' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;

                case'<!':
                    if ('[' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;

                case'<![':
                    if ('C' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;

                case'<![C':
                    if ('D' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;

                case'<![CD':
                    if ('A' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;

                case'<![CDA':
                    if ('T' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;

                case'<![CDAT':
                    if ('A' == $v) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;

                case'<![CDATA':
                    if ('[' == $v) {


                        $cdata = '';
                        $state = 'in';
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                    break;

                case'in':
                    if (']' == $v) {
                        $state = $v;
                    } else {
                        $cdata .= $v;
                    }
                    break;

                case']':
                    if (']' == $v) {
                        $state = $state . $v;
                    } else {
                        $cdata .= $state . $v;
                        $state = 'in';
                    }
                    break;

                case']]':
                    if ('>' == $v) {
                        $new_xml .= str_replace('>', '&gt;', str_replace('>', '&lt;', str_replace('"', '&quot;', str_replace('&', '&amp;', $cdata))));
                        $state = 'out';
                    } else {
                        $cdata .= $state . $v;
                        $state = 'in';
                    }
                    break;
            } // switch
        }
        return $new_xml;
    }

    private function simplest_xml_to_array($xmlstring) {
        return json_decode(json_encode((array) simplexml_load_string($xmlstring)), true);
    }

    //威富通-微信扫码回调通知地址
    public function wft_callback(){
        vendor('Wft.request');
        $req = new Request();
        $res = $req->callback();
    } 
}
