<?php

//书库
class StacksAction extends LoadAction {

    //小说类型/男女/属性/状态/点击
    public function index($type, $gender, $vip, $state, $click) {
        $cache = A('Cache');
        $guizhe = "type_$_GET[type]_gender_$_GET[gender]_vip_$_GET[vip]_state_$_GET[state]_click_$_GET[click]_p_$_GET[p]";
        //$guizhe = "type_$_GET[type]_gender_2_vip_$_GET[vip]_state_$_GET[state]_click_$_GET[click]_p_$_GET[p]";
        $cache->chushi("/Html_Cache/" . $this->website[web_id] . "/Stacks/", $guizhe);
        $cache->read_cache(); //读取缓存   
        //小说类型
        if ($type != 0) {
            $con['type_id'] = $type;
        } else {
            $this->assign('type', 'activite');
        }
        //判断男女
        if ($gender == 1) {
            $con['gender'] = 1; //男
            $this->assign('gender1', 'active');
        } else {
            $con['gender'] = 2; //女
            $this->assign('gender2', 'active');
        }
        //vip
        switch ($vip) {
            case 0:$con['vip'] = $vip;
                $this->assign('vip0', 'activite');
                break; //按章收费   
            case 1:$con['vip'] = $vip;
                $this->assign('vip1', 'activite');
                break; //按本收费  
            case 2:$con['vip'] = $vip;
                $this->assign('vip2', 'activite');
                break; //免费 
            case 3:
                $this->assign('vip3', 'activite');
                break; //不限             
        }
        //小说类型
        if ($state != 0) {
            $con['state'] = $state;
        } else {
            $this->assign('state', 'activite');
        }
        //点击榜
        switch ($click) {
            case 1:$click = 'new_time desc';
                $this->assign('cl0', 'activite');
                break; //正常              
            case 2:$click = 'click_total desc';
                $this->assign('cl1', 'activite');
                break; //总榜
            case 3:$click = 'click_month desc';
                $this->assign('cl2', 'activite');
                break; //月榜  
            case 4:$click = 'click_weeks desc';
                $this->assign('cl3', 'activite');
                break; //周榜
            case 5:$click = 'click_day desc';
                $this->assign('cl4', 'activite');
                break; //日榜              
        }
        //小说类型
        $this->assign('types', BooktypeAction::booktype());
        $this->base($con, $click);
        $cache->create_cache(); //生成缓存 
    }

    //公用方法
    public function base($con, $order) {
        $book = D('ListView');
        import('ORG.Util.Page'); // 导入分页类
        $con['web_id']= $this->website['web_id'];
        $con['Book.is_show'] = 1;
        $con['BookContent.attribute'] = array('lt', date('Y-m-d H:i:s', time()));
        $con['Book.fu_book'] = array('not in','10277,6326,7967,4111,9576,6444,6297,9535,5787,9920,99605, 10343, 78569, 78579,99597,10210');
        $count = $book->where($con)->count(); // 查询满足要求的总记录数 
        $Page = new Page($count, 10); // 实例化分页类 传入总记录数和每页显示的记录数
        $m = $book->where($con)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $Page->setConfig('theme', " <span class=\"pager_left\">%upPage%&nbsp;</span> <span class=\"pager_conn\">%nowPage%/%totalPage%</span> <span class=\"pager_right\">&nbsp;%downPage%</span>");
        $show = $Page->show(); // 分页显示输出
        $this->assign('list', $m);
        $this->assign('page', $show);
        $this->display();
    }

}
