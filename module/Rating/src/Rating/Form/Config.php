<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Rating\Form;

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
        parent::__construct('rating_config');
        $this->setAttributes(array(
            'class'=> 'normal-form ajax_submit',
            'action'=> url('admin/rating/config')
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'npGuestStatus',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'RATING_NP_GUEST_SHOW',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes'
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(new \System\Form\Buttons('rating_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
