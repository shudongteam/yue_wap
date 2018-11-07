<?php
    if (isset($_SESSION['randcode'])) {
        unset($_SESSION['randcode']);
    }
?>
<?php

    $authnum = random(5);
    header("Content-Type:image/png");
    $img = @imagecreate(90, 30) or die("you can't create image ");
    $background_color = imagecolorallocate($img, 255, 255, 255);
    $text_color = imagecolorallocate($img, 0, 0, 0);
    $gray = ImageColorAllocate($img, 102, 102, 0); //设置杂点颜色 
    imagefill($img, 55, 18, $background_color);
    for ($i = 0; $i < 10; $i++)
        imagesetpixel($img, rand() % 55, rand() % 18, $gray); //加入干扰象素 
    for ($i = 0; $i < strlen($authnum); $i++) {
        imagestring($img, 10, 12 * $i + 4, 10, substr($authnum, $i, 1), $text_color);
    }
    imagepng($img);
    imagedestroy($img);

    function random($length) {
        $hash = '';
        $chars = '1234567890';
        $max = strlen($chars) - 1;
        mt_srand((double) microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
        return $hash;
    }

//写入session
    $_SESSION['randcode'] = $authnum;
?>