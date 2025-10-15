<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Sms\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class Config extends BaseForm
{
    public function __construct()
    {
        parent::__construct('sms_config');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
            'action' => url('admin/sms/config'),
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'User Name'
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'type' => 'Zend\Form\Element\Password',
            'options' => array(
                'label' => 'Password'
            ),
        ));

        $this->add(array(
            'name' => 'from',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'From Number'
            ),
        ));

        $this->add(array(
            'name' => 'panel',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Sms Panel',
                'value_options' => array(
                    'PaneleSmsCom' => 'panelesms.com',
                    'SmsIR' => 'sms.ir',
                )
            ),
        ));

        $this->add(new \System\Form\Buttons('sms_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
