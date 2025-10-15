<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace User\Form;

use Application\API\App;
use System\Captcha\CaptchaFactory;
use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class PasswordRecovery extends BaseForm
{

    private $allowSendSms = 0;

    public function __construct($allowSendSms)
    {
        $this->allowSendSms = $allowSendSms;
        parent::__construct('password_recovery_form');
        $this->setAttributes(array(
            'class' => 'normal-form',
            'action' => url('app/user/password-recovery')
        ));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(
                'class' => ''
            )
        ));

        if ($this->allowSendSms) {
            $this->add(array(
                'name' => 'send-sms',
                'type' => 'Zend\Form\Element\Checkbox',
                'options' => array(
                    'label' => 'Send sms ?',
                    'description' => '',
                ),
            ));
        }

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_password_recovery_form'
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
//        $captcha = new Element\Captcha('captcha');
//        $captcha->setCaptcha($image);
//        $captcha->setAttribute('class', 'captcha');

        $this->add(CaptchaFactory::create());

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-send',
            'attributes' => array(
                'value' => 'Send',
                'class' => 'btn btn-default',
            )
        ));

    }


    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        #filter by digit only

        $this->filterByTrimAndTags($filter, array(
            'email',
        ));


        $email = $filter->get('email');
        $email
            ->setAllowEmpty(true)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress());
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());
    }

}
