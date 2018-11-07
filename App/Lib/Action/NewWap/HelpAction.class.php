<?php

//帮助文档
class HelpAction extends LoadingAction {

    public function index() {
        $where['web_id'] = $this->website[web_id];
        $where['type']=1;
        $ishelp = M('WebHelp')->where($where)->field('id,title')->select();
        $this->assign('ishelp', $ishelp);
        $this->display();
    }

    public function help($id) {
        $ishelp = M('WebHelp')->where(array('id' => $id))->find();
        $this->assign('ishelp', $ishelp);
        $this->display();
    }

}
