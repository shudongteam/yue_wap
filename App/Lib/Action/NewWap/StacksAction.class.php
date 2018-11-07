<?php

//书库
class StacksAction extends LoadingAction {

    //小说类型/男女/属性/状态/点击
    public function index($type = 0, $gender = 0, $vip = 3, $state = 0) {
        $cache = A('Cache');
        $guizhe = "type_$_GET[type]_gender_$_GET[gender]_vip_$_GET[vip]_state_$_GET[state]";
        $cache->chushi("/Html_Cache/NewWap/" . $this->website[web_id] . "/Stacks/", $guizhe);
        $cache->read_cache(); //读取缓存   
        $bookpromote = D('NBooktuiView');

        if ($type != 0) {
            $con['type_id'] = $type;     
        } else {
            $this->assign('type', 'fenleibian');
        }
        if ($gender == 1) {
            $con['gender'] = 1; //男
            $this->assign('gender1', 'fenleibian');
            $types = array(
            '1' => "悬疑",'2' => "历史",'3' => "军事",'4' => "玄幻",'5' => "奇幻",
            '6' => "仙侠",'7' => "武侠",'8' => "科幻",'9' => "游戏",'10' => "同人",'11' => "都市",
            );
            $where['promote_id'] = 15;
            $todaybooks = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,author_name')->limit(10)->order('xu asc')->select();
        } elseif($gender == 2) {
            $con['gender'] = 2; //女
            $this->assign('gender2', 'fenleibian');
            $types = array(
            '1' => "悬疑",'6' => "仙侠",'8' => "科幻",'10' => "同人",'11' => "都市",
            '12' => "校园",'13' => "言情",'14' => "穿越",'15' => "重生",'16' => "豪门",'17' => "职场",);
            $where['promote_id'] = 16;
            $todaybooks = $bookpromote->where($where)->field('book_id,book_name,upload_img,book_brief,author_name')->limit(10)->order('xu asc')->select();
        }else{
             $this->assign('genders', 'fenleibian');
             $types = BooktypeAction::booktype();
        }
        switch ($vip) {
            case 0:$con['vip'] = $vip;
                $this->assign('vip0', 'fenleibian');
                break; //按章收费   
            case 1:$con['vip'] = $vip;
                $this->assign('vip1', 'fenleibian');
                break; //按本收费  
            case 2:$con['vip'] = $vip;
                $this->assign('vip2', 'fenleibian');
                break; //免费 
            case 3:
                $this->assign('vip3', 'fenleibian');
                break; //不限             
        }
        if ($state == 1) {
            $con['state'] = 1;  
             $this->assign('state1', 'fenleibian');   
        } elseif($state == 2) {
            $con['state'] = 2; 
            $this->assign('state2', 'fenleibian');
        }else{
            $this->assign('state0', 'fenleibian');
        }
        $click = "buy_total desc";
        //小说类型
        //$this->assign('types', BooktypeAction::booktype());
        $this->assign('types', $types);
        $this->assign('todaybooks', $todaybooks);
        $this->base($con, $click);
        $cache->create_cache(); //生成缓存 
    }

    //公用方法
    public function base($con, $order) {
        $book = D('NBookListView');
        import('ORG.Util.Page'); // 导入分页类
        $con['Book.fu_book'] = array('exp', " = Book.book_id");
        $con['Book.book_id'] = undisplay();
        $con['Book.is_show'] = 1;
        //$con['BookContent.attribute'] = array('lt', date('Y-m-d H:i:s', time()));
        $data = $book->where($con)->order($order)->limit(10)->select();
        $this->assign('list', $data);
        $this->display();
    }

    public function get_data(){
        //$order = "click_month desc";
        $num = isset($_POST['num']) ? $_POST['num'] : 1;
        $type = isset($_POST['type']) ? $_POST['type'] : 0;
        $gender = isset($_POST['gender']) ? $_POST['gender'] : 0;
        $state = isset($_POST['state']) ? $_POST['state'] : 0;
        $vip = isset($_POST['vip']) ? $_POST['vip'] : 3;
        $cache_key = "Stacks_type_".$type."_gender_".$gender."_state_".$state."_vip_".$vip."_num_".$num;
        $data = S($cache_key);
        if (!$data) {
            if ($type) {
                $con['Book.type_id'] = $type;
            }
            if ($gender) {
                $con['Book.gender'] = $gender;
            }
            if ($state) {
                $con['Book.state'] = $state;
            }
            if ($vip != 3) {
                $con['Book.vip'] = $vip;
            }
            $book = D('NBookListView');
            $con['Book.fu_book'] = array('exp', " = Book.book_id");
            $con['Book.book_id'] = undisplay();
            $con['Book.is_show'] = 1;
          //  $con['BookContent.attribute'] = array('lt', date('Y-m-d H:i:s', time()));
            $data['flag'] = 0;
            $data['data'] = array();
            $res = $book->where($con)->order("buy_total desc")->limit($num*10,10)->select();
            // echo $book->getLastSql();
            if ($res) {
                $data['data'] = $res;
                $data['flag'] = 1;
            }
            S($cache_key, $data, 3600*4);
        }
        echo json_encode($data);
    }
}
