<?php

//收藏类
class NCollectionViewModel extends ViewModel {

    public $viewFields = array(
        'NBookCollection' => array('id','book_id','book_name', 'time'),
        'Book' => array('fu_book','_on' => 'NBookCollection.book_id=Book.book_id'),
        'BookContent' => array('num','title', '_on' => 'Book.fu_book=BookContent.fu_book AND BookContent.num = NBookCollection.chapter'),
    );
}
