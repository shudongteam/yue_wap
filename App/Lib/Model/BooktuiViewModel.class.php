<?php
    class BooktuiViewModel extends ViewModel {

        public $viewFields = array(
            'BookPromote' => array('id', 'book_id', 'web_id', 'promote_id','xu','book_name','upload_img','book_brief'),
            'Book' => array('author_name','fu_book','type_id', '_on' => 'BookPromote.book_id=Book.book_id'),
        );

    }
    