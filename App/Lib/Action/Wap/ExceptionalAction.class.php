<?php

//赠送礼物
class ExceptionalAction extends LoadAction {

    public function index($book) {
        if (!cookie('user_id')) {
              login();//调用登录
        }
        $this->assign('book', $book);
        $this->display();
    }

    public function add($book, $cpi) {
        if ($this->isPost()) {
            $jieguo = $this->chuli($book, $cpi);
            echo $jieguo;
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

    //处理礼物
    public function chuli($book, $cpi) {
        //查询是什么类型的礼物
        $liwu = $this->liwu($cpi);
        //礼物数量
        $num = (int) $_POST[num];
        if (is_array($liwu) && $num > 0) {
            //查询用户信息
            $uuu = M('User');
            $user = $uuu->where(array('user_id' => cookie('user_id')))->field('user_name,pen_name,user_pass,alance')->find();
            $shell2 = md5($user['user_name'] . $user['user_pass'] . $this->website[all_ps]);
            if ($shell2 == cookie('shell')) {
                $zong = $liwu[money] * $num;
                if ($user[alance] >= $zong) {
                    $books = M('Book')->where(array('book_id' => $book))->field('book_name')->find();
                    //扣钱
                    $vipvote = $zong / 100;
                    $map['vipvote'] = array('exp', "vipvote+$vipvote");
                    $map['alance'] = array('exp', "alance-$zong");
                    $isoq = $uuu->where(array('user_id' => cookie('user_id')))->save($map);
                    if ($isoq) {
                        //添加消费表
                        $datas['user_id'] = cookie('user_id');
                        $datas['book_id'] = $book;
                        $datas['type'] = 2;
                        $datas['money'] = $zong;
                        $datas['dosomething'] = "赠送：$books[book_name] $_POST[num] 个$liwu[name]";
                        $datas['time'] = date('Y-m-d H:i:s', time());
                        M('UserConsumerecord')->add($datas);
                        //更新粉丝记录
                        A('Fans')->index($book, $zong);
                        //添加礼物记录
                        $data['book_id'] = $book;
                        $data['user_id'] = cookie('user_id');
                        $data['pen_name'] = $user[pen_name];
                        $data['book_name'] = $books[book_name];
                        $data['num'] = $_POST[num];
                        $data['gift'] = $liwu[img];
                        $data['money'] = $zong;
                        $data['time'] = date('Y-m-d H:i:s', time());
                        M('BookExceptional')->add($data);
                        //添加留言记录
                        $datar['z_id'] = 0;
                        $datar['book_id'] = $book;
                        $datar['user_id'] = cookie('user_id');
                        $datar['good'] = 1;
                        $datar['title'] = "<font color=#fcb421>赠送：<img src=\"/Public/Gift/$liwu[img]\" width=\"17\" height=\"17\" /> × $_POST[num] 个礼物给作者！</font>";
                        if (empty($_POST[message])) {
                            $datar['content'] = "支持作者文思泉涌，妙笔生花";
                        } else {
                            $datar['content'] = $_POST['message'];
                        }
                        $datar['time'] = $data['time'];
                        M('BookMessage')->add($datar);
                        //跟新数据排名号
                        $saves['exceptional_day'] = array('exp', "exceptional_day+$zong");
                        $saves['exceptional_weeks'] = array('exp', "exceptional_weeks+$zong");
                        $saves['exceptional_month'] = array('exp', "exceptional_month+$zong");
                        $saves['exceptional_total'] = array('exp', "exceptional_total+$zong");
                        M('BookStatistical')->where(array('book_id' => $book))->save($saves);
                        //删除缓存
                        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $book));
                        $Cache->rm('index');
                        return 1;
                    } else {
                        return "系统错误";
                    }
                } else {
                    return "阅读币不足";
                }
            } else {
                return "系统错误请重新登录";
            }
        } else {
            return "数量不可未空";
        }
    }

}
