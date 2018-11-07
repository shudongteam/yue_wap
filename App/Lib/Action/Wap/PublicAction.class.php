<?php 
class PublicAction extends LoadAction{
    //验证码输出
    public function verify(){
        import('ORG.Util.Image');
        Image::buildImageVerify(4, 1, 'png', 75, 30);
    }

    //前端验证码验证
    public function check_verify(){
    	if (session('verify') != md5($_POST['verify'])) {
 		  	echo "false";
		} else {
			echo "true";
		}
    }

    //前端验证账户是否存在
    public function check_username(){
        $user_name = do_str($_POST[username]);
        $flag = "false";
        if (!empty($user_name)) {
            $where['web_id'] = C('Web_id');
            $where['user_name'] = $user_name;
            $count = M('user')->where($where)->sum('user_id');
            if ($count == 0) {
                $flag = "true";
            }
        }
        echo $flag;
    }

    //前端验证修改密码原密码是否正确
    public function check_pass() {
        $shell = md5($_POST[oldpassword] . C('ALL_ps'));
        if ($shell == $this->to['user_pass']) {
            echo "true";    
        } else {
            echo "false";
        }
    }

    //定时更新时间请晚于上一章节更新时间
    public function check_attribute() {
        $attribute = $_POST[attribute] ? strtotime($_POST[attribute]) : 0;
        $book_id = $_POST[book_id] ? $_POST[book_id] : 0;
        if ($attribute && $book_id) {
            $where = array("fu_book" => $book_id);
            $res = M("BookContent")->where($where)->order('content_id desc')->find(); 
            if ($res) {
                $res_attr = strtotime($res['attribute']);
                if ($attribute >= $res_attr) {
                    echo "true";
                } else {
                    echo "false";
                }
            }else{
                echo "true";
            }
        }
    }

    //检查留言是否含有关键字
    public function check_keyword() {
        $res = M('SystemKeys')->find(1);
        if ($res) {
            $data = explode(',', $res['key']);
            foreach ($data as $key => $value) {
                $data[$key] = preg_replace('/\s+/', '', $value);
            }
            $data = array_filter($data);
            // var_dump($data);
            $content = $_POST[content] ? $_POST[content] : '';
            $title = $_POST[title] ? $_POST[title] : 'ok';

            $regx = join("|", $data);
            if (preg_match("/".$regx."/", $content) || preg_match("/".$regx."/", $title)) {
                echo "false";
            } else {
                echo "true";
            }
        }
    }
}