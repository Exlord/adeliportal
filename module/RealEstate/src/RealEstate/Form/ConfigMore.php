<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace RealEstate\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class ConfigMore extends BaseForm
{
    private $users_list;

    public function __construct($users_list)
    {
        $this->users_list = $users_list;
        parent::__construct('real_estate_config_more');
        $this->setLabel('Real Estate Configs');
        //this form required the data to be manipulated before submitting so for now no ajax_submit
        $this->setAttributes(array(
            'class' => 'normal-form ',
            'action' => url('admin/real-estate/config/more')
        ));
    }

    protected function addElements()
    {
        if (!empty($this->users_list)) {
            $this->add(array(
                'name' => 'defaultAgent',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Default Real-Estate Agent',
                    'description' => "The Estates created by normal users will be assigned to this Agent, And in the estates list this agent information wil be shown to the visitors",
                    'value_options' => $this->users_list
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
        }
        $this->add(array(
            'name' => 'estateType_regType',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                'label' => 'Disabled Items',
                'description' => ''
            )
        ));

        $this->add(array(
            'name' => 'estateType_fields',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                'label' => 'Disabled Fields',
                'description' => 'The Selected Fields will be disabled'
            )
        ));

        /*$this->add(array(
             'name' => 'regType_fields',
             'type' => 'Zend\Form\Fieldset',
             'options' => array(
                 'label' => 'Disabled Fields',
                 'description' => 'The Selected Fields will be disabled'
             )
         ));*/


        $this->add(new Buttons('real_estate_config_more', array(Buttons::SAVE)));
    }

    protected function addInputFilters()
    {
//        $filter = $this->getInputFilter();
//        $estateType_regType = $filter->get('estateType_regType');
    }
}
