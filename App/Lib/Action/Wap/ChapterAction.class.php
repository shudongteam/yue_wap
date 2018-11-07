<?php

//章节类
class ChapterAction extends LoadAction {

    //需要调用的方法
    private $user; //用户表
    private $buy; //购买表
    private $bookcontent; //章节表
    //需要储存的数据
    private $userinfo; //用户信息
    private $books; //作品
    private $content; //内容
    private $buyinfo; //购买信息

    public function index($book, $num) {
        $this->promoter();
        //设置缓存方式跟时间/文件缓存/时间3600秒/缓存路径
        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $book));
        $this->books = $Cache->get("chapter");
        if (!is_array($this->books)) {
            //找书
            #特殊处理 103462, 103606, 命运掌控者(微信杀人)
            if ($book == 103462 || $book == 103606) {
                $this->books = M('Book')->where(array('book_id' => $book))->field('book_id,fu_book,book_name,type_id,vip,money')->find();
            } else {
                $this->books = M('Book')->where(array('book_id' => $book))->field('book_id,fu_book,book_name,type_id,vip,money')->find();
                //$this->books = M('Book')->where(array('book_id' => $book, 'is_show' => 1))->field('book_id,fu_book,book_name,type_id,vip,money')->find();
            }
            if (is_array($this->books)) {
                //更新缓存
                $Cache->set("chapter", $this->books);
            } else {
                $this->error("没有找到该书");
                exit();
            }
        }
        //内容缓存
        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $this->books[fu_book]));
        $this->content = $Cache->get($num);
        $this->bookcontent = M('BookContent');
        if (!is_array($this->content)) {
            //生成内容缓存
            $con = $this->bookcontent->where(array('fu_book' => $this->books[fu_book], 'num' => $num, 'attribute' => array('lt', date('Y-m-d H:i:s', time()))))->field('content_id,num,title,number,the_price,attribute')->find();
            if (is_array($con)) {
                $content = M('BookContents')->where(array('content_id' => $con['content_id']))->find();
                $content['content'] = str_replace("\n", "</p><p>", str_replace(" ", "", $content[content]));
                $this->content = array_merge($con, $content);
                $Cache->set($num, $this->content);
            } else {
                //没有内容
                $this->zhangjie();
                exit();
            }
        }
        //更新点击数量
        $this->bookcontent->switchModel('Adv')->where(array('content_id' => $this->content[content_id]))->setLazyInc("clicknum", 1, 300);
        //全部显示显示出来
        $this->assign('content', $this->content);
        $this->assign('books', $this->books);
        //强制关注
        if ($_GET[focus]) {
            $this->assign('focus', "?focus=$_GET[focus]");
            if ($_GET[focus] == $num) {
                $qrcode_url = '/Upload/code/'.$this->website[web_id].'.jpg';
                if($this->website[web_id] == 7) {
                        $qrcode = array(1,2,3,4,5);
                        $arr = array();
                        foreach ($qrcode as $key => $value) {
                            echo $file = $virtualPath = '/Upload/code/' . $this->website[web_id] . '-sj' . $value. '.jpg';
                            if (file_exists($file)) {
                                $arr[] = $value;
                            }
                        }
                        if ($arr) {
                            shuffle($arr);
                            $qrcode_url = '/Upload/code/'.$arr[0].'.jpg';
                        }
                }

                $this->assign('qrcode_url', $qrcode_url);
                $this->display('/Cheapter:focus'); //强制关注
                exit();
            }
        }
        //阅读记录
        $user_id = cookie('user_id');
        if ($user_id) {
            //阅读记录
            $coll = M('BookCollection');
            $iscoll = $coll->where(array('book_id' => $this->books[book_id], 'user_id' => $user_id))->field('id')->find();
            if (is_array($iscoll)) {
                $coll->where(array('book_id' => $this->books[book_id], 'user_id' => $user_id))->save(array('chapter' => $num));
            } else {
                //没有收藏就收藏
                $data[book_id] = $this->books[book_id];
                $data[user_id] = $user_id;
                $data[book_name] = $this->books[book_name];
                $data[chapter] = $num;
                $data[time] = date('Y-m-d H:i:s', time());
                $coll->add($data);
                $datas['collection_day'] = array('exp', "collection_day+1");
                $datas['collection_weeks'] = array('exp', "collection_weeks+1");
                $datas['collection_month'] = array('exp', "collection_month+1");
                $datas['collection_total'] = array('exp', "collection_total+1");
                M('BookStatistical')->where(array('book_id' => $this->books[book_id]))->save($datas);
            }
        }

        //章节显示部分
        $chapterinfo['preid'] = $num <= 1 ? '1' : $num - 1;
        $chapterinfo['nextid'] = $num + 1;
        $this->assign('chapterinfo', $chapterinfo);
        
        //判断是否收费的
        if ($this->content['the_price']) {
            //vip类型
            if ($this->books['vip'] != 2) {
                //调用验证
                $this->user();
                //调用购买判断方法
                $this->buy();
            }
        }

        //底部推荐位
        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Data/' . $this->website[web_id]));
        $procheapter = $Cache->get("procheapter");
        if (!$procheapter) {
            $procheapter = M('BookPromote')->where(array('promote_id'=>14,'web_id'=>$this->website[web_id]))->order('xu asc')->limit(6)->select();
            $Cache->set("procheapter", $procheapter);
        }
        $this->assign('procheapter',$procheapter);




        //判断是否开启 红包活动
        if($num%5==0 && $num >= 30){
            $red = 1;
            $this->assign("activity_red", $red);
        }
        //判断是否开启 自动签到
        $alance = 0;
        if($user_id){
            $sign = cookie('is_sign');
            //已过期, 或者小于当前时间 触发签单
            if (!$sign || $sign < time()) {
                $user = M('User');
                $where['user_id'] = $user_id;
                $myuser = $user->where($where)->field('sign_time')->find();
                if ($myuser[sign_time] != date("Y-m-d", time())) {
                    $alance = mt_rand(30, 50);
                    $data['alance'] = array('exp', "alance+{$alance}"); 
                    $data['sign_num'] = array('exp', "sign_num+1");;
                    $data['sign_time'] = date("Y-m-d");
                    $user->where($where)->save($data);
                    // 过期时间设置为明天凌晨0点过期
                    $e_time = strtotime(date('Y-m-d', strtotime('+1 day')));
                    cookie('is_sign', $e_time, $e_time);
                }
            }
        }
        $this->assign("alance", $alance);
        $this->display();
    }

    //用户验证方法
    private function user() {
        if ($_COOKIE['user_id']) {
            //创建用户表
            $this->user = M('User');
            //查询信息
            $user = $this->user->where(array('user_id' => $_COOKIE['user_id']))->field('user_id,user_name,user_pass,alance,vip_time')->find();
            $shell2 = md5($user['user_name'] . $user['user_pass'] . $this->website[all_ps]);
            if ($shell2 == cookie('shell')) {
                $this->userinfo = $user;
            } else {
                login(); //调用登录
                exit();
            }
        } else {
            if($this->website[web_id] == 1 || $this->website[web_id] == 25){
                login(); //调用登录
                exit(); 
            }else {
                $back_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                header('location:/Accounts/weixin.html?backUrl=' . $back_url);
                exit;
            }
        }
    }

    //创建购买信息        
    private function buyjudge() {
        //创建购买表
        $this->buy = M('BookBuy');
        //判断是否购买
        $con['user_id'] = $this->userinfo[user_id];
        $con['book_id'] = $this->books[book_id];
        $this->buyinfo = $this->buy->where($con)->field('chapter_id')->find();
        if (!is_array($this->buyinfo)) {
            //登录后判断章节记录是否存在不存在创建
            $data['book_id'] = $this->books[book_id];
            $data['user_id'] = $this->userinfo[user_id];
            $data['chapter_id'] = ":";
            $ok = $this->buy->add($data);
            if ($ok) {
                $this->buyinfo = $data;
            } else {
                $this->error("系统错误");
            }
        }
    }

    //调用购买
    public function buy() {
        //判断是否包月
        if (!$this->baoyue()) {
            $this->buyjudge();
            switch ($this->books['vip']) {
                //按章
                case 0:
                    if (!strstr($this->buyinfo['chapter_id'], $this->content[content_id])) {
                        $this->anzhang(); //按章收费
                    }
                    break;
                //按本
                case 1:
                    if (!strstr($this->buyinfo['chapter_id'], "ben")) {
                        $this->anben(); //按本收费
                    }
                    break;
            }
        }
    }

    //按章收费
    public function anzhang() {
        $is = $this->consumption($this->content[content_id], $this->content[the_price]);
        if ($is != 1) {
            $this->display('/Cheapter:chongzhi'); //钱不够显示充值
            exit();
        } else {
            //购买成功产生记录
            $this->jilubiao($this->content[the_price], "购买：" . $this->books[book_name] . "：" . $this->content[title]);
            //添加购买次数
            $this->mycishu();
        }
    }

    //按本收费
    public function anben() {
        //购买按本书籍提示
        if (!isset($_GET['anben'])) {
            $this->display('/Cheapter:bentps'); 
            exit;
        }

        $is = $this->consumption("ben", $this->books[money]);
        if ($is != 1) {
            $this->display('/Cheapter:chongzhi'); //钱不够显示充值
            exit();
        } else {
            //购买成功产生记录
            $this->jilubiao($this->books[money], "整本购买：" . $this->books[book_name]);
        }
    }

    //消费记录
    public function jilubiao($money, $xinxi) {
        $datas['user_id'] = $this->userinfo[user_id];
        $datas['book_id'] = $this->books[book_id];
        $datas['type'] = 1;
        $datas['money'] = $money;
        $datas['dosomething'] = $xinxi;
        $datas['time'] = date('Y-m-d H:i:s', time());
        M('UserConsumerecord')->add($datas);
    }

    //添加购买次数
    public function mycishu() {
        $this->bookcontent->switchModel('Adv')->where(array('content_id' => $this->content[content_id]))->setLazyInc("dycs", 1, 300);
    }

    //包月
    private function baoyue() {
        if (strtotime($this->userinfo[vip_time]) > strtotime(date('Y-m-d H:i:s', time()))) {
            return TRUE; //ok
        } else {
            return FALSE; //没i有开通包月
        }
    }

    //存储/价格
    public function consumption($shuju, $money) {
        if ($this->userinfo[alance] >= $money) {
            //扣钱
            $map['alance'] = array('exp', "alance-$money");
            $this->user->where(array('user_id' => $this->userinfo['user_id']))->save($map);
            //增加章节数据记录
            $dataaa['chapter_id'] = array('exp', "CONCAT(chapter_id,',$shuju')");
            $this->buy->where(array('book_id' => $this->books[book_id], 'user_id' => $this->userinfo['user_id']))->save($dataaa);
            //更新粉丝记录
            A('Fans')->index($this->books[book_id], $money);
            //跟新数据排名号
            $saves['buy_day'] = array('exp', "buy_day+$money");
            $saves['buy_weeks'] = array('exp', "buy_weeks+$money");
            $saves['buy_month'] = array('exp', "buy_month+$money");
            $saves['buy_total'] = array('exp', "buy_total+$money");
            M('BookStatistical')->where(array('book_id' => $this->books[book_id]))->save($saves);
            return 1;
        } else {
            return 2;
        }
    }

    //好书推荐
    public function zhangjie() {
        $bookpromote = M('BookPromote');
        $arr = $bookpromote->where(array('promote_id' => 8,'web_id'=>$this->website[web_id]))->field('book_id,book_name,upload_img')->limit(8)->order('xu asc')->select();
        $this->assign('meiyou', $arr);
        $this->display("/Cheapter_promote");
    }

    //获取推广ID
    public function promoter() {
        //推广人
        if ($_GET[agent]) {
            cookie('agent', $_GET[agent], time() + 2 * 7 * 24 * 3600);
        }
        //渠道ID
        if ($_GET[channel]) {
            M('AgentChannel')->where(array('channel_id' => $_GET[channel]))->save(array('click_total' => array('exp', "click_total+1")));
            cookie('channel', $_GET[channel], time() + 2 * 7 * 24 * 3600);
        }
    }
}
