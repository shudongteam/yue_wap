<?php

//收藏类
class CollectionViewModel extends ViewModel {

    public $viewFields = array(
        'BookCollection' => array('id','book_id','book_name', 'chapter'),
        'Book' => array('_on' => 'BookCollection.book_id=Book.book_id'),
        'BookContent' => array('title', '_on' => 'Book.fu_book=BookContent.fu_book AND BookContent.num = BookCollection.chapter'),
    );
}
