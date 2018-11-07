<?php
#章节查看（没有菜单）
class ChaptertwoAction extends LoadingAction {

    //需要调用的方法
    private $money; //钱表
    private $buy; //购买表
    //需要储存的数据
    private $userinfo; //用户信息
    private $books; //作品
    private $content; //内容
    private $buyinfo; //购买信息

    public function index($book, $num) {

        //设置缓存方式跟时间/文件缓存/时间3600秒/缓存路径
        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $book));
        $this->books = $Cache->get("chapter");
        if (!is_array($this->books)) {
            #没有缓存进行缓存处理
            $this->books = M('Book')->where(array('book_id' => $book))->field('book_id,fu_book,book_name,type_id,vip,money')->find();
            if (is_array($this->books)) {
                //更新缓存
                $Cache->set("chapter", $this->books);
            } else {
                $this->error("没有找到没有该书");
                exit();
            }
        }

        #获取内容缓存
        $Cache = Cache::getInstance('File', array('expire' => '3600', 'temp' => RUNTIME_PATH . 'Temp/' . $this->books[fu_book]));
        $this->content = $Cache->get($num);
        if (!is_array($this->content)) {
            //生成内容缓存
            $con = M('BookContent')->where(array('fu_book' => $this->books[fu_book], 'num' => $num))->field('content_id,num,title,number,the_price')->find();
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
        // M('BookContent')->where(array('content_id' => $this->content[content_id]))->save(array('clicknum' => array('exp', "clicknum+1")));
        M('BookContent')->switchModel('Adv')->where(array('content_id' => $this->content[content_id]))->setLazyInc("clicknum", 1, 3600);

        //全部显示显示出来
        $this->assign('content', $this->content);
        $this->assign('books', $this->books);
        //阅读记录
        $user_id = $_COOKIE['user_id'];
        if ($user_id) {
            //阅读记录
            $data[book_id] = $this->books[book_id];
            $data[book_name] = $this->books[book_name];
            $data[title] = $this->content[title];
            $data[chapter] = $num;
            $key = 'collect';
            $path = RUNTIME_PATH.'collect/' . $user_id;
            $collect = F($key, '', $path);
            // var_dump($collect);
            if ($collect) {
                foreach ($collect as $k => $value) {
                    if ($value == $data[book_id]) {
                        unset($collect[$k]);
                        $collect = array_values($collect);
                        break;
                    }
                }
                $count = count($collect);
                if ($count >= 50) {
                    F($key.'_'.$collect[0], NUll, $path);
                    unset($collect[0]);
                    $collect = array_values($collect);                
                }
                array_push($collect, $data[book_id]);
            } else {
                $collect = array($data[book_id]);
            }
            F($key, $collect, $path);
            F($key.'_'.$data[book_id], $data, $path);
        }
        //保存连接首次参数，点击数增加一次
        if($_GET['channel']&&$_GET['agent']){
            cookie('channel', $_GET[channel], time() +   2 * 7 * 24 * 3600);
            cookie('agent', $_GET[agent], time() +  2 * 7 * 24 * 3600);
            $ac = M('NAgentChannel');
            $datas['channel_id'] = $_GET[channel];
            $datas['click_total'] = array('exp', "click_total+1");
            $ac->save($datas);
            //echo $ac->getlastsql();
        }

        // 强制关注
        if ($_GET[focus]) {
            $this->assign('focus', "?focus=$_GET[focus]");
            if ($_GET[focus] == $num) {
                // $qrcode_url = '/Upload/code/'.$this->website[web_id].'.jpg';
                $qrcode_url=$this->website['weixin'];
                $this->assign('qrcode_url', $qrcode_url);
                $this->display('/Chapter:focus'); //强制关注
                exit();
            }
        }

        //判断是否收费的
        if ($this->content['the_price']>0) {
            //vip类型
            if ($this->books['vip'] != 2) {
                //调用验证
                $this->user();
                //调用购买判断方法
                $this->buy();
            }
        }
        //章节显示部分
        $chapterinfo['preid'] = $num <= 1 ? '1' : $num - 1;
        $chapterinfo['nextid'] = $num + 1;
        $this->assign('chapterinfo', $chapterinfo);
        #作者推荐
        $this->author();
        $this->display();
    }

    //用户验证方法
    private function user() {
        $user_id = $_COOKIE['user_id'];
        if ($user_id) {
            $this->usel_shell($user_id, $_COOKIE['shell']);
            $this->money = M('NUserMoney');
            $this->userinfo = $this->money->where(array('user_id' => $user_id))->find();
        } else {
            $back_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            header('location:/Login/index.html?backUrl=' . $back_url);
            exit;
        }
    }

    //创建购买信息        
    private function buyjudge() {
        //创建购买表
        $this->buy = M('NBookBuy');
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
        $is = $this->consumption("ben", $this->books[money]);
        if ($is != 1) {
            $this->display('/Cheapter:chongzhi'); //钱不够显示充值
            exit();
        } else {
            //购买成功产生记录
            $this->jilubiao($this->books[money], "整本购买：" . $this->books[book_name]);
        }
    }

    //存储/价格
    public function consumption($shuju, $money) {

        if ($this->userinfo[alance] >= $money) {
            //扣钱
            $map['alance'] = array('exp', "alance-$money");
            $this->money->where(array('user_id' => $this->userinfo['user_id']))->save($map);
            //增加章节数据记录
            $dataaa['chapter_id'] = array('exp', "CONCAT(chapter_id,',$shuju')");
            $this->buy->where(array('book_id' => $this->books[book_id], 'user_id' => $this->userinfo['user_id']))->save($dataaa);
            //更新粉丝记录
            $this->fans($this->books[book_id], $money);
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

    //添加购买次数
    public function mycishu() {
        M('BookContent')->where(array('content_id' => $this->content[content_id]))->save(array('dycs' => array('exp', "dycs+1")));
    }

    //消费记录
    public function jilubiao($money, $xinxi) {
        $datas['user_id'] = $this->userinfo[user_id];
        $datas['book_id'] = $this->books[book_id];
        $datas['type'] = 1;
        $datas['money'] = $money;
        $datas['dosomething'] = $xinxi;
        $datas['time'] = date('Y-m-d H:i:s', time());
        M('NUserConsumerecord')->add($datas);
    }

    //书籍ID 增加的粉丝的书量
    public function fans($bookid, $num) {
        //粉丝职
        $fans = M('NBookFans');
        $con[book_id] = $bookid;
        $con[user_id] = $this->userinfo['user_id'];
        $fansvalue = $fans->where($con)->field('fan_value')->find();
        //查看粉丝表在不在
        if (!is_array($fansvalue)) {
            $myinfo = M('NUserInfo')->where(array('user_id' => $this->userinfo['user_id']))->field('pen_name')->find();
            $data['book_id'] = $bookid;
            $data['user_id'] = $this->userinfo['user_id'];
            $data['pen_name'] = $myinfo['pen_name'];
            $data['fan_value'] = $num;
            $data['time'] = date('Y-m-d H:i:s', time());
            $fans->add($data);
        } else {
            $data['fan_value'] = array('exp', "fan_value+$num");
            $fans->where($con)->save($data);
        }
    }

    //包月
    private function baoyue() {
        if (strtotime($this->userinfo[vip_time]) > strtotime(date('Y-m-d H:i:s', time()))) {
            return TRUE; //ok
        } else {
            return FALSE; //没i有开通包月
        }
    }

    //作者推荐
    public function author() {
        $haoshu = S('haoshu');
        if (!$haoshu) {
            $bookpromote = M('NBookPromote');
            $where['promote_id'] = 12;
            $arr = $bookpromote->where($where)->field('book_id,book_name,book_brief')->limit(4)->order('xu asc')->select();
            $this->assign('haoshu', $arr);
            S('haoshu', $arr, 3600);
        } else {
            $this->assign('haoshu', $haoshu);
        }
    }
    //好书推荐
    public function zhangjie() {
        $bookpromote = M('NBookPromote');
        $arr = $bookpromote->where(array('promote_id' => 11))->field('book_id,book_name,upload_img')->limit(4)->order('xu asc')->select();
        $this->assign('meiyou', $arr);
        $this->display("/Cheapter_promote");
    }

        //判断是否真实登录
    private function usel_shell($user_id, $shell) {
        $User = M('NUser');
        $where['user_id'] = $user_id;
        $isUser = $User->field('`user_name`,`user_pass`')->where($where)->find();
        if ($isUser) {
            $shell2 = md5($isUser['user_name'] . $isUser['user_pass'] . C('ALL_ps'));
            if ($shell != $shell2) {
                login();//调用登录
                exit();
            }
        } else {
            login();//调用登录
            exit();
        }
    }
}
