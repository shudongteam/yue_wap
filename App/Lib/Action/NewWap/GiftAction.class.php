<?php

//礼包
class GiftAction extends GlobalAction {

    //首冲礼包界面
    public function shouchong() {
        $this->display();
    }

    //微信礼包界面
    public function weixin() {
        $this->display();
    }

    //获取
    public function libaoadd($type) {
        switch ($type) {
            case 1:
                $uuu = M('User')->where(array('user_id' => $this->to['user_id'], 'money' => 0))->field('user_id')->find();
                if (is_array($uuu)) {
                    echo "请充值后在领取";
                } else {
                    $iss = $this->shengcheng(1);
                    if ($iss == 1) {
                        $this->shou();
                    }
                }
                break;
            case 2:
                $iss = $this->shengcheng(2);
                if ($iss == 1) {
                    $this->weishouchong();
                }
                break;
            default:
                echo "系统错误";
                break;
        }
    }

    //生成礼包码
    public function shengcheng($type) {
        $gift = M('SystemGift');
        //判断是否使用过礼包码
        $mylibao = $gift->where(array('user_id' => $this->to[user_id], 'type' => $type))->find();
        if (is_array($mylibao)) {
            echo "您已经领取了该礼包";
            exit();
        }
        //生成方法
        $data['user_id'] = $this->to[user_id];
        $data['type'] = $type;
        $data['time'] = date('y-m-d H:i:s', time());
        $rid = $gift->add($data);
        if (!$rid) {
            echo "系统错误"; //错误
            exit();
        } else {
            return 1;
        }
    }

    //首冲礼包
    private function shou() {
        $data['vote'] = array('exp', "vote+5");
        $data['vipvote'] = array('exp', "vipvote+2");
        $data['alance'] = array('exp', "alance+200");
        $is = M('User')->where(array('user_id' => $this->to[user_id]))->save($data);
        if ($is) {
            echo 1;
        } else {
            echo "系统错误";
        }
    }

    //微首冲礼包
    private function weishouchong() {
        $data['vote'] = array('exp', "vote+10");
        $data['vipvote'] = array('exp', "vipvote+5");
        $data['alance'] = array('exp', "alance+200");
        $is = M('User')->where(array('user_id' => $this->to[user_id]))->save($data);
        if ($is) {
            echo 1;
        } else {
            echo "系统错误";
        }
    }

}
