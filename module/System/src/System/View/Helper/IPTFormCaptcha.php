<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/15/14
 * Time: 1:28 PM
 */

namespace System\View\Helper;


use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormCaptcha;

class IPTFormCaptcha extends FormCaptcha
{
    public function render(ElementInterface $element)
    {
        $captcha = parent::render($element);
        return sprintf("<div class='captcha-box'>%s</div>", $captcha);
    }
} 