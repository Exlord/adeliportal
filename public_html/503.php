<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/17/13
 * Time: 2:14 PM
 */
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 3600');
?>
<html lang="fa" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>
        سایت به صورت موقت در دسترس نیست
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/system.css">
    <link rel="stylesheet" type="text/css" href="/themes/default/css/style.css">
    <style type="text/css">
        body{overflow-y:hidden;}
        .latin *{ font-family:'Courier New', Monospace; text-align:left; }
        .box{ float:right; padding:30px; height:120px; margin:10px;min-width:240px; }
        .box.latin{ }
        .box h3{ font-size:25pt; }
        .content{ max-width:800px; margin:auto; }
        .left{ float:left; }
        ._503{position:fixed;bottom:20px;right:20px;width:200px;height:150px;}
        ._503 h3.page-title{font-size:65pt;}
    </style>
</head>
<body>
<div class="content">
    <div dir="rtl" class="box right">
        <div>
            <h3 class="page-title">
                خارج از دسترس
            </h3>

            <p class="description">
                سایت به صورت موقت در دسترس نمیباشد
            </p>
        </div>
    </div>
    <div dir="ltr" class="box latin left">
        <h3 class="page-title">
            Unavailable
        </h3>

        <p class="description">
            The site is temporally unavailable
        </p>
    </div>
    <div dir="ltr" class="box latin right">
        <h3 class="page-title">
            Yok
        </h3>

        <p class="description">
            Site geçici olarak kullanılamıyor
        </p>
    </div>
    <div dir="ltr" class="box latin left">
        <h3 class="page-title">
            əlçatmaz
        </h3>

        <p class="description">
            Site müvəqqəti unavailable edir
        </p>
    </div>
    <div class="_503">
        <h3 class="page-title">503</h3>
    </div>
</div>
</body>
</html>
