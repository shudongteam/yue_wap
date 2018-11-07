<?php

//微信回调接口
class WeiAction extends Action {
      // public function index(){
      //         $nonce     = $_GET['nonce'];
      //         $token     = 'chunguang';
      //         $timestamp = $_GET['timestamp'];
      //         $echostr   = $_GET['echostr'];
      //         //只有第一次做token验证的时候才会有$_GET['echostr'],所以这个变量的有无代表着是否与服务器第一次交互，如果不是则代表第一次你们在互相通信
      //         $signature = $_GET['signature'];
      //         //形成数组，然后按字典序排序
      //         $array = array();
      //         $array = array($nonce, $timestamp, $token);
      //         sort($array);
      //         //拼接成字符串,sha1加密 ，然后与signature进行校验
      //         $str = sha1( implode( $array ) );
      //        //if( $str  == $signature && $echostr ){
      //         if( $str  == $signature && $echostr){
      //             //第一次接入weixin api接口的时候
      //             echo  $echostr;
      //             exit;
      //         }
      //     }
    private $fromUsername; //发送方帐号（一个OpenID）
    private $toUsername; //开发者微信号
    private $times; //当前时间
    private $Content; //发来的文本信息
    private $MsgType; //消息累心
    protected $website; //网站数据
    protected $wiki;//公众号id

    public function index($wiki) {
    	// 如果为修改配置
    	if ($_GET['nonce'] && $_GET['echostr']) {
    		echo  $_GET['echostr'];
    		exit();
    	} else {
          $this->wiki = $wiki;
    		  //载入工具包
	        $gogju = A('Gongju');
          $this->website = $gogju->webssite();
	        $this->responseMsg();
    	}
    }

    //获取post数据
    public function responseMsg() {
    	
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->fromUsername = $postObj->FromUserName; //发送方帐号（一个OpenID）
            $this->toUsername = $postObj->ToUserName; //获取开发者微信号
            $this->Content = trim($postObj->Content); //传送来的信号（代码含义：去除前后空格）
            $this->MsgType = trim($postObj->MsgType); //消息类型
            $this->times = time(); //获取时间 
            //消息类型判断
            switch ($this->MsgType) {
                case "text":
                    $this->receiveText();
                    break;
                case "event":
                    $this->receiveEvent($postObj);
                    break;
                default:
                    $this->noknow();
                    break;
            }
        }
    }

    //txt消息判断
    public function receiveText() {

        switch ($this->Content) {
            case "客服":
                $this->kefu();
                break;
            default:
                $this->zhaoshu();
                break;
        }
    }

    //事件判断            
    private function receiveEvent($object) {
        switch ($object->Event) {
            case "subscribe":
                $this->guanzhu($object); //关注推送
                break;
            case "CLICK":
                $this->clicks($object->EventKey); //菜单点击事件
                break;
            default:
                $this->transmitText("谢谢");
                break;
        }
    }

    // 餐单点击推送
    private function clicks($keys) {
        //如果点击的是推荐位
        if(strpos($keys, "promote")!==false){
            $arr = explode('_', $keys);
            $this->tuwen($arr[1]);
            exit;
        }
        switch ($keys) {
            case 'kefus':
                $contentStr = "我们的联系方式为！\r\n微信：" . $this->website[weixinkefu]."\r\nQ Q：" . $this->website[qq] . " \r\n电话：" . $this->website[phone];
                $this->transmitText($contentStr);
                break;
            // case 'jingpinnvpin':
            //     $this->tuwen(12);
            //     break;
            // case 'rexiaoguyan':
            //     $this->tuwen(13);
            //     break;
            case 'fuli':
                $contentStr = "每日签到送礼<a href=\"http://".$this->website[web_url]."/Personal/Sign/index.html\">【点击签到】</a>\r\n领取微信礼包<a href=\"http://".$this->website[web_url]."/Personal/Sign/weixin.html\">【点击领取】</a>\r\n领取首充礼包<a href=\"http://".$this->website[web_url]."/Personal/Sign/first.html\">【点击领取】</a>";
                $this->transmitText($contentStr);
                break;
            default:
            	$this->clicksWiki($keys);
                break;
        }
        exit();
    }
    
    // 点击推送
    private function clicksWiki($keys) {
    	$result = M('WikiMenu')->where(array('key' => "$keys", 'wiki_id' => $this->wiki))->find();
    	
    	if ($result) {
    		switch ($result['type']) {
    			case 'click':
    				$content = trim(strip_tags($result['content'], "<a>\r"));
    				$this->transmitText($content);
    				break;
    			case 'click_news':
    				$this->transmitPic($result['content']);
    				break;
    		}
    		
    	} else {
    		$this->noknow();
    	}
    }
    
    //获取图文信息
    public function transmitPic($content)
    {
    	$result = unserialize($content);
    	if (count($result)) {
    		$textTpl = '<xml>
					<ToUserName><![CDATA[' . $this->fromUsername . ']]></ToUserName>
					<FromUserName><![CDATA[' . $this->toUsername . ']]></FromUserName>
					<CreateTime>' . $this->times . '</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>' . count($result) . '</ArticleCount>
					<Articles>';
    		foreach ($result as $value)
    		{
    			$textTpl .= '<item>
					<Title><![CDATA[' . $value['title'] . ']]></Title> 
					<Description><![CDATA[' . $value['description'] . ']]></Description>
					<PicUrl><![CDATA[' . 'http://admin.ktread.com/Upload/weixin/' . $value['img'] . ']]></PicUrl>
					<Url><![CDATA[' . $value['url'] . ']]></Url>
					</item>';
    		}
    	
			$textTpl .=	'</Articles>
						</xml>';
			echo $textTpl;
			exit();
    	} else {
    		$this->noknow();
    		exit();
    	}
    }
    

    //不知道什么消息           
    private function noknow() {
        $this->transmitText($this->buxiangguan());
    }

    //信息转接到客服
    public function kefu() {
        $textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			</xml>";

        $msgType = "transfer_customer_service"; //回复类型
        $resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $this->times, $msgType);
        echo $resultStr;
    }

    //找书
    private function zhaoshu() {
      $keyword = do_keyword($this->Content);
      $len = mb_strlen($keyword);
      if ($len >=6) {
          $books = M('book')->where(array('book_name' => array('like', "%$keyword%"), 'web_id' => $this->website[web_id], 'is_show' =>1))->field('book_id,book_name,book_brief,upload_img')->order('level desc')->limit(4)->select();
          if (is_array($books)) {
              $num = count($books);
              //准备模板    
              $textTpl = "<xml>
                              <ToUserName><![CDATA[$this->fromUsername]]></ToUserName>
                              <FromUserName><![CDATA[$this->toUsername]]></FromUserName>
                              <CreateTime>$this->times</CreateTime>
                              <MsgType><![CDATA[news]]></MsgType>
                              <ArticleCount>$num</ArticleCount>
                              <Articles>";
              foreach ($books as $key => $value) {
                  $book_name = $value['book_name'];
                  // $book_brief = msubstr($value['book_brief'], 0, 30);
                  $book_brief = $value['book_brief'];
                  $textTpl.= "<item>
                              <Title><![CDATA[$book_name]]></Title> 
                              <Description><![CDATA[$book_brief]]></Description>
                              <PicUrl><![CDATA[http://".$this->website[web_url]."/Upload/Book/xiao/$value[upload_img]]]></PicUrl>
                              <Url><![CDATA[http://".$this->website[web_url]."/books/$value[book_id].html]]></Url>
                              </item>";
              }
              $textTpl.= "
                              </Articles>
                              </xml>  ";
                echo $textTpl;
                exit;
          }
        }
        $con = M('WeixinAutomatic')->where(array('web_id' => $this->website[web_id], 'title' => array('like',"%$this->Content%")))->find();
        if ($con) {
            $this->transmitText($con[content]);
        } else {
            $this->transmitText($this->buxiangguan());
        }
    }

    //关注推送信息       
    private function guanzhu($object) {
    	
        // $result = M("Wiki")->where(array('web_id' => $this->website['web_id'], 'original_id' => "$this->toUsername"))->find();
		    $result = M("Wiki")->where(array('web_id' => $this->website['web_id'], 'wiki_id' => "$this->wiki"))->find();
        $contentStr = $result['content'];
        // $gogju1 = A('Gongju');
        // $arr = $gogju1->chapter($web);
        //die();
        if (!$contentStr) {
          $contentStr = "亲爱的读者，欢迎关注" . $this->website[web_name] . "，我们为您提供言情小说、青春小说、悬疑小说等各种小说类型，海量原创作品供您选择！\r\n接着上次看<a href=\"http://".$this->website[web_url]."/Personal/Bookcase/reading.html\">【继续阅读】</a>\r\n领取微信礼包<a href=\"http://".$this->website[web_url]."/Personal/Sign/weixin.html\">【点击领取】</a>\r\n请记得每日签到领取阅读币！";
        	//$contentStr = "亲爱的读者，欢迎关注" . $this->website[web_name] . "，我们为您提".$arr."各种小说类型，海量原创作品供您选择！\r\n接着上次看<a href=\"http://w.ymbook.cn/Bookcase/reading/web/". $this->website[web_id] . ".html\">【继续阅读】</a>\r\n领取微信礼包<a href=\"http://w.ymbook.cn/Gift/weixin/web/" . $this->website[web_id] . ".html\">【点击领取】</a>\r\n如果网站有什么问题请联系我们！我们将给予奖励！";
        }
        
        $this->transmitText($contentStr);
    }

    //回复信息
    private function transmitText($contentStr) {
        $textTpl = "<xml>
			<ToUserName><![CDATA[%s]]></ToUserName>
			<FromUserName><![CDATA[%s]]></FromUserName>
			<CreateTime>%s</CreateTime>
			<MsgType><![CDATA[%s]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";

        $msgType = "text"; //回复类型
        $resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $this->times, $msgType, $contentStr);
        echo $resultStr;
        exit();
    }

    //没有找到东西        
    public function buxiangguan() {
        if($this->website['web_id'] == 14){
            $str = '1.您发送朋友圈截图参与免费看书活动，请提供账户ID，客服将24小时内审阅截图，联系您充值，请留意帐号；

2.<a href="'.$this->website[web_url].'"> 去首页转转</a>

3. 如遇充值、阅读异常等问题，请联系客服微信：13056068534 ; Q Q：2134869403。

4.点击⇩左下方菜单栏阅读记录，可以查看您的阅读记录。';
        }else{
          $str = "对不起，找不到相关的小说您可以试试:
1. 去首页转转<a href='http://".$this->website[web_url]."'>【☞请戳这里】</a>
2. 大家都爱看<a href='http://".$this->website[web_url]."/Top/index/id/9.html'>【☞请戳这里】</a>
3. 点击⇩左下方阅读记录，可以查看您的阅读记录" ;
        }
        return $str;
    }

    //图文消息模板
    public function tuwen($id) {
        //准备数据                
        $bookpromote = M('bookPromote');
        $where['web_id'] = $this->website[web_id];
        $where['promote_id'] = $id;
        $arr = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief')->limit(3)->order('xu asc')->select();
        $num = count($arr);
        //准备模板    
        $textTpl = "<xml>
                        <ToUserName><![CDATA[$this->fromUsername]]></ToUserName>
                        <FromUserName><![CDATA[$this->toUsername]]></FromUserName>
                        <CreateTime>$this->times</CreateTime>
                        <MsgType><![CDATA[news]]></MsgType>
                        <ArticleCount>$num</ArticleCount>
                        <Articles>";
        foreach ($arr as $key => $value) {


            $textTpl.= "<item>
                        <Title><![CDATA[$value[book_brief]]]></Title> 
                        <Description><![CDATA[$value[book_brief]]]></Description>
                        <PicUrl><![CDATA[http://".$this->website[web_url]."/Upload/Book/zhong/$value[upload_img]]]></PicUrl>
                        <Url><![CDATA[http://".$this->website[web_url]."/books/$value[book_id].html]]></Url>
                        </item>";
        }
        $textTpl.= "
                        </Articles>
                        </xml>  ";
        echo $textTpl;
    }


    //生成菜单

   // public function createmenu() {
   //     //春色服务
   //     $appid  = "wx96efa6f66a9dea1f"; //微信appid
   //     $secret = "a465e6c0d8ac91751de0fe80d0fdadb4";
   //     //得到access_token
   //     $access_token = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret");
   //     $access_obj = json_decode($access_token);
   //     $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_obj->access_token";
   //     // echo "<pre>";
   //     // print_r($access_token);
   //     $arr = array(
   //         'button' => array(
   //             array(
   //                 'name' => urlencode("好书推荐"),
   //                 'sub_button' => array(
   //                     array(
   //                         'name' => urlencode("首页搜书"),
   //                         'type' => 'view',
   //                         "url" => "http://w.ymbook.cn/Index/index/web/15.html",
   //                     ),
   //                     array(
   //                         'name' => urlencode("往期精彩"),
   //                         'type' => 'view',
   //                         "url" => "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzI1NjEwMzYxMw==&scene=124#wechat_redirect",
   //                     ),
   //                     array(
   //                         'name' => urlencode("小说排行"),
   //                         'type' => 'view',
   //                         "url" => "http://w.ymbook.cn/Rankinglist/index/web/15.html",
   //                     ),
   //                     array(
   //                         'name' => urlencode("精品女频"),
   //                         'type' => 'click',
   //                         "key" => "jingpinnvpin",
   //                     ),
   //                     array(
   //                         'name' => urlencode("热销古言"),
   //                         'type' => 'click',
   //                         "key" => "rexiaoguyan",
   //                     ),
   //                 )
   //             ),
   //             array(
   //                 'name' => urlencode("继续阅读"),
   //                 'sub_button' => array(
   //                     array(
   //                         'name' => urlencode("继续阅读"),
   //                         'type' => 'view',
   //                         "url" => "http://w.ymbook.cn/Bookcase/reading/web/15.html",
   //                     ),
   //                     array(
   //                         'name' => urlencode("粉丝福利"),
   //                         'type' => 'click',
   //                         "key" => "fuli",
   //                     ),
   //                     array(
   //                         'name' => urlencode("我的书架"),
   //                         'type' => 'view',
   //                         "url" => "http://w.ymbook.cn/Bookcase/index/web/15.html",
   //                     ),
   //                     array(
   //                         'name' => urlencode("个人中心"),
   //                         'type' => 'view',
   //                         "url" => "http://w.ymbook.cn/Personal/index/web/15.html",
   //                     )
   //                 )
   //             ),
   //             array(
   //                 'name' => urlencode("充值"),
   //                 'sub_button' => array(
   //                     array(
   //                         'name' => urlencode("我要充值"),
   //                         'type' => 'view',
   //                         "url" => "http://w.ymbook.cn/Pay/index/web/15.html",
   //                     ),
   //                     array(
   //                         'name' => urlencode("联系客服"),
   //                         'type' => 'click',
   //                         "key" => "kefus",
   //                     ),
   //                 )
   //             )
   //         )
   //     );
   //     $jsondata = urldecode(json_encode($arr));
   //     $ch = curl_init();
   //     curl_setopt($ch, CURLOPT_URL, $url);
   //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   //     curl_setopt($ch, CURLOPT_POST, 1);
   //     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsondata);
   //     echo $ch;
   //     $r = curl_exec($ch);
   //     print_r($r);
   //     curl_close($ch);
   //     echo $appid;
   // }
}
