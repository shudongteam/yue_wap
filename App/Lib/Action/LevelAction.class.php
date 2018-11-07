<?php

//用户等级
class LevelAction {

    //充值等级
    static function paylevel($exp) {
        $arr = array(6 => 96000, 5 => 48000, 4 => 12000, 3 => 4000, 2 => 200, 1 => 0);
        for ($i = count($arr); $i >= 1; $i--) {
            if ($arr[$i] <= $exp) {
                return $i;
            }
        }
    }

    //vip等级
    static function viplevel($exp) {
        $arr = array(17 => 1000000, 16 => 800000, 15 => 500000, 14 => 200000, 13 => 150000, 12 => 100000, 11 => 80000, 10 => 50000, 9 => 30000, 8 => 20000, 7 => 10000, 6 => 8000, 5 => 5000, 4 => 2000, 3 => 1000, 2 => 500, 1 => 0,);
        for ($i = count($arr); $i >= 1; $i--) {
            if ($arr[$i] <= $exp) {
                return $i;
            }
        }
    }

}
