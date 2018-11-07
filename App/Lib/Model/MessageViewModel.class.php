<?php
//留言模块
class MessageViewModel  extends  ViewModel{
   public $viewFields = array(
     'BookMessage'=>array('f_id','z_id','top','good','book_id','user_id','title','content','num','time'),
     'User'=>array('user_id','pen_name','portrait','_on'=>'BookMessage.user_id=User.user_id'),
   );
}