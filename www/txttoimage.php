<?php
if (!$_GET['value']) {
    die('no value given');
}

if (($_SERVER['HTTP_HOST'] == 'nolotiro.org') || ($_SERVER['HTTP_HOST'] == 'nolotiro.dev')) {

    header("Content-type: image/png");

    $value = str_replace('xxx', '', $_GET['value']); //this is to parse emails addres from helper escapeemail
    $length = (strlen($value) * 7.5);
    $im = @ImageCreate($length, 20)
            or die("no esta instalada GD");
    $background_color = ImageColorAllocate($im, 255, 255, 255);
    $text_color = ImageColorAllocate($im, 44, 44, 44);
    imagestring($im, 3, 5, 2, $value, $text_color);
    imagepng($im);
} else {
    die('not allowed server');
}

