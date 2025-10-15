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

class GroupList extends BaseForm
{
    private $groupSelect;
    public function __construct($groupSelect)
    {
        $this->groupSelect = $groupSelect;
        parent::__construct('groups_form');
        $this->setAttributes(array(
            'class', 'normal-form',
            'action'=>url('admin/online-orders/item-select')
        ));
    }
    protected function addElements()
    {



        $this->add(array(
            'name' => 'groupList',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Categories',
                'value_options' => $this->groupSelect
            ),
            'attributes' => array(
                'class' => 'group-list-item select2',
            ),

        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-show',
            'attributes' => array(
                'value' => 'Show',
                'class' => 'button',
            ),

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
