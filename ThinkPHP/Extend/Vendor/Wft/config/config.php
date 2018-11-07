<?php
class Config{
    private $cfg = array(
        'url'=>'https://pay.swiftpass.cn/pay/gateway',
        'mchId'=>'101540075688',//测试商户号，商户上线需改为自己正式的
        'key'=>'e74e9b29459f5d0227f9a0633287aa6e',//测试密钥，商户上线需改为自己正式的
        'version'=>'2.0',
		'notify_url'=>'http://w.ymzww.cn/Personal/Notify/wft_callback/'//异步回调通知地址，商户上线需改为自己正式的
       );
    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }
}
?>