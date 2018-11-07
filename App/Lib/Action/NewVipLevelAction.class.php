<?php

//用户等级
class NewVipLevelAction {

    public function viplevel($integral) {
        if ($integral >= 3000) {
            return 6;
        } elseif ($integral >= 1800) {
            return 5;
        } elseif ($integral >= 1000) {
            return 4;
        } elseif ($integral >= 500) {
            return 3;
        } elseif ($integral >= 200) {
            return 2;
        } else {
            return 1;
        }
    }
}
