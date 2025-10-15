<?php

namespace OnlineOrders\API;

class Captcha
{
    public function createCaptcha()
    {
        $md5_hash = md5(rand(0, 999));
        $security_code = substr($md5_hash, 15, 5);
        $_SESSION['captcha_code'] = $security_code;
        return $security_code;
    }

}