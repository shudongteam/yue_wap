<?php

//作品类型类
class BooktypeAction {

    //作品类型
    static function type() {
        $tytes = array(
            '1' => "悬疑",
            '2' => "历史",
            '3' => "军事",
            '4' => "玄幻",
            '5' => "奇幻",
            '6' => "仙侠",
            '7' => "武侠",
            '8' => "科幻",
            '9' => "游戏",
            '10' => "同人",
            '11' => "都市",
            '12' => "校园",
            '13' => "言情",
            '14' => "穿越",
            '15' => "重生",
            '16' => "豪门",
            '17' => "职场",
        );
        return $tytes;
    }

    //返回作品类型
    static function booktype() {
        return self::type();
    }

    //返回具体类型
    static function mybooktype($booktype) {
        return self::type()[$booktype];
    }

}
