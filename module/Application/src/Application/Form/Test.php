<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/18/14
 * Time: 3:52 PM
 */

namespace Application\Form;


use Application\API\App;
use System\Captcha\CaptchaFactory;
use System\Captcha\Factory;
use System\Form\BaseForm;

class Test extends BaseForm
{

    public function __construct()
    {
        parent::__construct('test_form');
        $this->setAction(url('app/test'));
    }

    protected function addElements()
    {
//        $image = new \System\Captcha\Math();
//        $image = new \System\Captcha\NumberImage();
//
////        $image = new \Zend\Captcha\Image();
//        $image->setFont(PUBLIC_PATH . '/fonts/arial.ttf');
//        $image->setImgDir(PUBLIC_FILE . '/captcha');
//        $image->setImgUrl(App::siteUrl() . '/clients/' . ACTIVE_SITE . '/files/captcha');
//        $image->setDotNoiseLevel(5);
//        $image->setWordlen(4);
//        $image->setFontSize(35);
//        $image->setWidth(150);
//        $image->setHeight(80);
//
//        $captcha = new \Zend\Form\Element\Captcha('captcha');
//        $captcha->setCaptcha($image);
//        $captcha->setAttribute('class', 'captcha');

//        $this->add(CaptchaFactory::create());
//        $this->add($captcha);

        $this->add(array(
            'type' => 'System\Form\Element\Constant',
            'name' => 'constant',
        ));
        $this->get('constant')->setValue('Test');

        $this->add(new \System\Form\Buttons('test_form'));
    }
}