<?php

//赠送礼物
class ExceptionalAction extends GlobalAction {

    public function gift($book) {
        if (!cookie('user_id')) {
              login();//调用登录
        }
        $this->assign('book', $book);
        $this->display();
    }

    public function index($bookid, $type, $bookname) {
        if ($_COOKIE['user_id']) {
            $liwu = $this->liwu($type);
            if (is_array($liwu)) {
                $where['user_id'] = $_COOKIE['user_id'];
                $money = M('NUserMoney');
                $alance = $money->where($where)->getField('alance');
                if ($alance >= $liwu['money']) {
                    #扣钱
                    $datakou['alance'] = array('exp', "alance-$liwu[money]");
                    $money->where($where)->save($datakou);
                    #粉丝
                    $this->fans($bookid, $_COOKIE['user_id'], $liwu['money']);
                    #礼物统计表
                    $exdata['num'] = array('exp', "num+1");
                    $giftname = "gift_" . $type;
                    $exdata[$giftname] = array('exp', "$giftname+1");
                    M('NBookExceptional')->where(array('book_id' => $bookid))->save($exdata);
                    //更新数据排名号
                    $saves['exceptional_day'] = array('exp', "exceptional_day+$liwu[money]");
                    $saves['exceptional_weeks'] = array('exp', "exceptional_weeks+$liwu[money]");
                    $saves['exceptional_month'] = array('exp', "exceptional_month+$liwu[money]");
                    $saves['exceptional_total'] = array('exp', "exceptional_total+$liwu[money]");
                    M('BookStatistical')->where(array('book_id' => $bookid))->save($saves);
                    //添加消费表
                    $datas['user_id'] = $_COOKIE['user_id'];
                    $datas['book_id'] = $bookid;
                    $datas['type'] = 2;
                    $datas['money'] = $liwu[money];
                    $datas['dosomething'] = "赠送：$bookname $liwu[name] 1 个";
                    $datas['time'] = date('Y-m-d H:i:s', time());
                    M('NUserConsumerecord')->add($datas);
                    //添加留言记录
                    $datar['z_id'] = 0;
                    $datar['audit'] = 2;
                    $datar['book_id'] = $bookid;
                    $datar['user_id'] =  $_COOKIE['user_id'];
                    $datar['good'] = 1;
                    $datar['pen_name']=$_COOKIE['pen_name'];
                    $datar['content'] = "<font color=#fcb421>赠送：<img src=\"/Public/Gift/images/$liwu[img]\" width=\"17\" height=\"17\" /> × 1 个礼物给作者！</font>";
                    $datar['time'] = $datas['time'];
                    M('NBookMessage')->add($datar);
                    echo 1;
                } else {
                    echo "阅读币不足";
                }
            } else {
                echo "没有找到礼物类型";
            }
        } else {
            
            echo "请先登录";exit();
        }
    }

    //礼物方法
    private function liwu($cpi) {
        switch ($cpi) {
            case 1:
                $liwu['img'] = "jinbi.png";
                $liwu['name'] = "金币";
                $liwu['money'] = 100;
                break;
            case 2:
                $liwu['img'] = "hongjiu.png";
                $liwu['name'] = "红酒";
                $liwu['money'] = 200;
                break;
            case 3:
                $liwu['img'] = "dangao.png";
                $liwu['name'] = "蛋糕";
                $liwu['money'] = 500;

                break;
            case 4:
                $liwu['img'] = "qiche.png";
                $liwu['name'] = "汽车";
                $liwu['money'] = 1000;
                break;
            case 5:
                $liwu['img'] = "feiji.png";
                $liwu['name'] = "飞机";
                $liwu['money'] = 5000;
                break;
            case 6:
                $liwu['img'] = "tianshi.png";
                $liwu['name'] = "天使";
                $liwu['money'] = 10000;
                break;
        }
        return $liwu;
    }

    //书籍ID 增加的粉丝的书量
    public function fans($bookid, $user_id, $num) {
        $myinfo = M('NUserInfo')->where(array('user_id' => $user_id))->field('pen_name')->find();
        //粉丝职
        $fans = M('NBookFans');
        $con[book_id] = $bookid;
        $con[user_id] = $user_id;
        $fansvalue = $fans->where($con)->field('fan_value')->find();
        //查看粉丝表在不在
        if (!is_array($fansvalue)) {
            $data['book_id'] = $bookid;
            $data['user_id'] = $user_id;
            $data['pen_name'] = $myinfo['pen_name'];
            $data['fan_value'] = $num;
            $data['time'] = date('Y-m-d H:i:s', time());
            $fans->add($data);
        } else {
            $data['fan_value'] = array('exp', "fan_value+$num");
            $fans->where($con)->save($data);
        }
    }

    //查看更多打赏
    public function addshang($bookid,$num){
         header('Content-Type:text/html; charset=utf-8');
         //$cont = M('n_user_consumerecord');
         $data = D('DashangView')->where(array('book_id' => $bookid,"type"=>2))->order('time desc')->limit("$num,3")->select();
        $this->ajaxReturn(json_encode($data),'JSON');
    }
}
