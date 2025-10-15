<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OnlineOrder\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;

class Config extends BaseForm
{
    private $mailTemplate =array();
    public function __construct($mailTemplate)
    {
        $this->mailTemplate = $mailTemplate;
        parent::__construct('online_order_config');
        $this->setAttributes(array(
            'class'=> 'normal-form',
            'action'=>url('admin/online-order/config'),
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'priceSilver',
            'type' => 'Zend\Form\Element\Text',
            'attributes'=>array(
                'class'=>'spinner online-order-input withcomma'
            ),
            'options' => array(
                'description' => t('Toman')
            ),
        ));

        $this->add(array(
            'name' => 'priceBronze',
            'type' => 'Zend\Form\Element\Text',
            'attributes'=>array(
                'class'=>'spinner online-order-input withcomma'
            ),
            'options' => array(
                'description' => t('Toman')
            ),
        ));

        $this->add(array(
            'name' => 'priceGold',
            'type' => 'Zend\Form\Element\Text',
            'attributes'=>array(
                'class'=>'spinner online-order-input withcomma'
            ),
            'options' => array(
                'description' => t('Toman')
            ),
        ));


        $this->add(array(
            'name' => 'domainPrice',
            'type' => 'Zend\Form\Element\Text',
            'attributes'=>array(
                'class'=>'spinner online-order-input withcomma'
            ),
            'options' => array(
                'description' => t('Toman'),
                'label'=>'Price for each Domain'
            ),
        ));

        $this->add(array(
            'name' => 'orderValidate',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Form of payment confirmation email',
                'empty_option' => '-- Select --',
                'value_options' => $this->mailTemplate,

            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'createSite',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Email template after making sub sites',
                'empty_option' => '-- Select --',
                'value_options' => $this->mailTemplate,

            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'RegOrder',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Mail the order form sub sites',
                'value_options' => $this->mailTemplate,
                'empty_option' => '-- Select --',
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));


        $this->add(new \System\Form\Buttons('online_order_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();



        #filter by digit only
        $this->filterByDigit($filter, array(
            'priceSilver',
            'priceBronze',
            'priceGold',
            'domainPrice',
        ));

        $this->setRequiredFalse($filter,array(
            'orderValidate',
            'createSite',
            'RegOrder'
        ));

    }
}
