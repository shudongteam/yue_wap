<?php

//充值接口
class PayccAction extends Action {

    //添加钱
    public function payadd($user_id, $agent_id, $integral, $web_id) {
        $data['user_id'] = $user_id;
        $data['agent_id'] = $agent_id;
        $data['web_id'] = $web_id;
        //渠道号
        if ($_COOKIE[channel]) {
            $data['channel_id'] = $_COOKIE[channel];
        } else {
            $data['channel_id'] = 0;
        }
        //是否包年
        if ($_POST[money] == 365) {
            $data['is_members'] = 1; //是包年
        }
        //充值类型
        switch ($_POST[payChannelType]) {
            case 1:
                $data['type'] = "微信充值";
                break;
            case 2:
                $data['type'] = "微信扫码";
                break;
            case 3:
                $data['type'] = "支付宝";
                break;
        }
        //生成订单
        $b = date('YmdHis');
        //生成随机订单号
        $data['trade'] = $b . mt_rand(1000000, 9999999);
        $data['money'] = $_POST[money];
        $data['readmoney'] = $_POST[money] * 100 + $this->duosong($_POST[money]) + $this->vip($_POST[money] * 100, $integral);
        $data['state'] = 1;
        $data['time'] = date('Y-m-d H:i:s', time());
        $isok = M('SystemPay')->add($data);
        if ($isok) {
            return $data; //数据处理进行数组返回
        } else {
            $this->error("系统错误");
        }
    }

    //多送阅读币            
    private function duosong($money) {
        //赠送礼物
        if ($money >= 200) {
            return 2400;
        } elseif ($money >= 150) {
            return 1200;
        } elseif ($money >= 100) {
            return 600;
        } elseif ($money >= 50) {
            return 400;
        } elseif ($money >= 30) {
            return 0;
        }
    }

    //vip等级
    private function vip($ymb, $integral) {
        $men_vip = LevelAction::paylevel($integral);
        if ($men_vip != 1) {
            switch ($men_vip) {
                case 2:
                    $ticheng = $ymb * 0.01;
                    break;
                case 3:
                    $ticheng = $ymb * 0.02;
                    break;
                case 4:
                    $ticheng = $ymb * 0.03;
                    break;
                case 5:
                    $ticheng = $ymb * 0.04;
                    break;
                case 6:
                    $ticheng = $ymb * 0.05;
                    break;
            }
            return $ticheng;
        } else {
            return 0;
        }
    }

    //添加钱
    public function payadd2($user_id, $agent_id, $integral, $web_id) {
        $data['user_id'] = $user_id;
        $data['agent_id'] = $agent_id;
        $data['web_id'] = $web_id;
        //渠道号
        if ($_COOKIE[channel]) {
            $data['channel_id'] = $_COOKIE[channel];
        } else {
            $data['channel_id'] = 0;
        }

        $data['is_members'] = 1; //是包年

        //充值类型
        $data['type'] = "微信充值(红包活动)";

        //生成订单
        $b = date('YmdHis');

        $money = 265;
        // $money = 1;

        //生成随机订单号
        $data['trade'] = $b . mt_rand(1000000, 9999999);
        $data['money'] = $money;
        $data['readmoney'] = 365 * 100 + $this->duosong(365) + $this->vip(365 * 100, $integral);
        $data['state'] = 1;
        $data['time'] = date('Y-m-d H:i:s', time());
        $isok = M('SystemPay')->add($data);
        if ($isok) {
            return $data; //数据处理进行数组返回
        } else {
            $this->error("系统错误");
        }
    }
    //添加钱
    public function payadd3($user_id, $agent_id, $integral, $web_id) {
        $data['user_id'] = $user_id;
        $data['agent_id'] = $agent_id;
        $data['web_id'] = $web_id;
        //渠道号
        if ($_COOKIE[channel]) {
            $data['channel_id'] = $_COOKIE[channel];
        } else {
            $data['channel_id'] = 0;
        }

        $data['is_members'] = 0; //不是包年

        //充值类型
        $data['type'] = "微信充值(圣诞充12元送12元)";

        //生成订单
        $b = date('YmdHis');

        $money = 12;
        // $money = 1;

        //生成随机订单号
        $data['trade'] = $b . mt_rand(1000000, 9999999);
        $data['money'] = $money;
        $data['readmoney'] = 2400;
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
