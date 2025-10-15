<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 10:21 AM
 */

namespace User\Form;

use Application\API\App;
use System\Captcha\CaptchaFactory;
use System\Form\BaseForm;
use System\Form\Buttons;
use System\Validator\Username;
use Zend\Captcha;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\I18n\Filter\Alnum;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Zend\Filter;

class Register extends BaseForm
{
    /**
     * @var Adapter
     */
    private $_adapter;
    private $general_details;
    private $countryId, $stateId, $cityId, $fields;

    protected $loadInputFilters = false;

    public function __construct(Adapter $adapter, $general_details, $countryId = array(), $stateId = array(), $cityId = array(), $fields = null)
    {
        $this->setAttribute('class', 'normal-form');
        $this->_adapter = $adapter;
        $this->general_details = $general_details;
        $this->countryId = $countryId;
        $this->stateId = $stateId;
        $this->cityId = $cityId;
        $this->fields = $fields;
        parent::__construct('user_register');
    }

    protected function addElements()
    {
        $this->add(new Basic());

        if ($this->general_details) {
            $profile = new Profile($this->countryId, $this->stateId, $this->cityId);
            $this->add($profile);
        }

        if ($this->fields) {
            $this->add(array(
                'type' => 'Zend\Form\Fieldset',
                'name' => 'profile2',
                'options' => array(
                    'label' => 'Custom Details'
                )
            ));
        }

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

        $this->add(new Buttons('register'));
    }

    public function addInputFilters($filters = array())
    {
        parent::addInputFilters($filters);
        /**
         * @var $password \Zend\InputFilter\Input
         * @var $password_verify \Zend\InputFilter\Input
         * @var $email \Zend\InputFilter\Input
         * @var $username \Zend\InputFilter\Input
         * @var $displayName \Zend\InputFilter\Input
         * @var $roles \Zend\InputFilter\Input
         */
        $filter = $this->getInputFilter();
        $basic = $filter->get('basic');

        $username = $basic->get('username');
        $username
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)))
            ->attach(new \Zend\Validator\Db\NoRecordExists(
                array(
                    'table' => 'tbl_users',
                    'field' => 'username',
                    'adapter' => $this->_adapter,
                    'messages' => array(
                        \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'This username is already taken'
                    )
                )
            ))
            ->attach(new Username());
        $username->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());
        //TODO add a filter to allow _


        $password = $basic->get('password');
        $password
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()->attach(new StringLength(array('min' => 5, 'max' => 25)));
        $password->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());


        $password_verify = $basic->get('password_verify');
        $password_verify
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)))
            ->attach(new Identical(array('token' => 'password',
                'messages' => array(Identical::NOT_SAME => 'Password and Verify Password do not match'))));
        $password_verify->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());


        $email = $basic->get('email');
        $email
            ->setAllowEmpty(true)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress())
            ->attach(new NoRecordExists(
                array(
                    'table' => 'tbl_users',
                    'field' => 'email',
                    'adapter' => $this->_adapter,
                    'messages' => array(
                        \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'This Email address belongs to another user'
                    )
                )));
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());


        $displayName = $basic->get('displayName');
        $displayName
            ->setAllowEmpty(true)
            ->setRequired(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)));
        $displayName->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());

        if ($this->general_details) {
            $profile = $filter->get('profile');
            $profile->get('countryId')->setRequired(false);
            $profile->get('countryId')->allowEmpty(true);
            $profile->get('stateId')->setRequired(false);
            $profile->get('stateId')->allowEmpty(true);
            $profile->get('cityId')->setRequired(false);
            $profile->get('cityId')->allowEmpty(true);
            $profile->get('mobile')->setRequired(true);
            $profile->get('mobile')->allowEmpty(false);
        }
    }
}
