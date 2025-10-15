<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OnlineOrders\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;

class OrderTracking extends BaseForm
{
    public function __construct()
    {
        parent::__construct('order-tracking_form');
        $this->setAttributes(array(
            'class', 'normal-form',
        ));
    }
    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(array(
            'name' => 'orderName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Order Name : '
            ),
        ));



        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_order_tracking_form'
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Order Tracking',
                'class' => 'button',
            )
        ));

    }


    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();

        //    $sampleFiled = $filter->get('sampleField');
    }

}
