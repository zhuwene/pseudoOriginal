<?php
require "../vendor/autoload.php";
use zhuweneTrans\PseudoOriginal;

$str = '我是文章的内容,测试下可不可以用.';

$cont = new PseudoOriginal($str);

echo $cont->content();
