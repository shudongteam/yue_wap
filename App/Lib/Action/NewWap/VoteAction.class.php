<?php

//VIP票
class VoteAction extends GlobalAction {

    //普通票
    public function vote($bookid) {
        if ($_COOKIE['user_id']) {
            $where['user_id'] = $_COOKIE['user_id'];
            $money = M('NUserMoney');
            $momeys = $money->where($where)->find();
            if ($momeys['vote'] > 0) {
                $this->fans($bookid, $_COOKIE['user_id'], 1);
                //排行榜
                $bookexceptional = M('BookStatistical');
                $data['vote_day'] = array('exp', "vote_day+1");
                $data['vote_weeks'] = array('exp', "vote_weeks+1");
                $data['vote_month'] = array('exp', "vote_month+1");
                $data['vote_total'] = array('exp', "vote_total+1");
                $bookexceptional->where(array('book_id' => $bookid))->save($data);
                $datas['vote'] = array('exp', "vote-1");
                $money->where($where)->save($datas);
                echo 1;
            } else {
                echo "阅明珠不足";
            }
        } else {
            echo "请先登录";
        }
    }

    //VIP票
    public function vipvote($bookid) {
        if ($_COOKIE['user_id']) {
            $where['user_id'] = $_COOKIE['user_id'];
            $money = M('NUserMoney');
            $momeys = $money->where($where)->find();
            if ($momeys['vipvote'] > 0) {
                $this->fans($bookid, $_COOKIE['user_id'], 3);
                //排行榜
                $bookexceptional = M('BookStatistical');
                $data['vipvote_day'] = array('exp', "vipvote_day+1");
                $data['vipvote_weeks'] = array('exp', "vipvote_weeks+1");
                $data['vipvote_month'] = array('exp', "vipvote_month+1");
                $data['vipvote_total'] = array('exp', "vipvote_total+1");
                $bookexceptional->where(array('book_id' => $bookid))->save($data);
                $datas['vipvote'] = array('exp', "vipvote-1");
                $money->where($where)->save($datas);
               echo 1;
            } else {
                // echo "金钻不足";
                echo "阅明珠不足";
            }
        } else {
            echo "请先登录";
        }
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

}
