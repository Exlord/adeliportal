<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/17/13
 * Time: 9:18 AM
 */

namespace Mail\Form;


use System\Captcha\CaptchaFactory;
use System\Form\BaseForm;
use Application\API\App;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;

class QuickSendMail extends BaseForm
{

    private $email = '';

    public function __construct($email)
    {
        $this->email = $email;
        parent::__construct('quick_send_mail');
        $this->setAttributes(array(
            'class' => 'normal-form',
            'action' => url('app/quick-send-mail'),
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'quick_send_mail_email_to',
            'type' => 'Zend\Form\Element\Hidden',
            'options' => array(),
            'attributes' => array(
                'value' => $this->email,
                'id' => 'quick_send_mail_email_to'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'quick_send_mail_name',
            'options' => array(
                'label' => 'Name',
                'description' => '',
                'column-size' => 'md-8 col-sm-8 col-xs-12',
                'label_attributes' => array('class' => 'col-md-4 col-sm-4 col-xs-12')
            ),
            'attributes' => array(
                'id' => 'quick_send_mail_name'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'quick_send_mail_email',
            'options' => array(
                'label' => 'Email',
                'description' => '',
                'column-size' => 'md-8 col-sm-8 col-xs-12',
                'label_attributes' => array('class' => 'col-md-4 col-sm-4 col-xs-12')
            ),
            'attributes' => array(
                'id' => 'quick_send_mail_email'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'quick_send_mail_text',
            'options' => array(
                'label' => 'Description',
                'description' => '',
                'column-size' => 'md-8 col-sm-8 col-xs-12',
                'label_attributes' => array('class' => 'col-md-4 col-sm-4 col-xs-12')
            ),
            'attributes' => array(
                'id' => 'quick_send_mail_text'
            )
        ));

//        $image = new \Zend\Captcha\Image();
//        $image->setFont(PUBLIC_PATH . '/fonts/arial.ttf');
//        $image->setImgDir(PUBLIC_FILE . '/captcha');
//        $image->setImgUrl(App::siteUrl() . '/clients/' . ACTIVE_SITE . '/files/captcha');
//        $image->setDotNoiseLevel(5);
//        $image->setWordlen(4);
//        $image->setFontSize(35);
//        $image->setWidth(150);
//        $image->setHeight(80);
//        $captcha = new Element\Captcha('quick_send_mail_captcha');
//        $captcha->setCaptcha($image);
//        $captcha->setAttribute('class', 'captcha');
        $captcha = CaptchaFactory::create('quick_send_mail_captcha');
        $captcha->setOptions(array(
            'column-size' => 'xs-12 text-left',
        ));
        $this->add($captcha);


        /*$this->add(array(
            'type' => 'submit',
            'name' => 'submit-send',
            'attributes' => array(
                'value' => 'Send',
                'class' => 'button',
            )
        ));*/
    }

    protected function addInputFilters()
    {
        // TODO: Implement addInputFilters() method.
    }
}