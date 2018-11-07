<?php
#新站推荐位
class NewBooktuiViewModel extends ViewModel {

        public $viewFields = array(
            'NBookPromote' => array('id', 'book_id', 'promote_id','xu','book_name','upload_img','book_brief'),
            'Book' => array('book_id','fu_book','author_name','type_id','state','new_time', '_on' => 'Book.book_id=NBookPromote.book_id'),
        );

    }
    