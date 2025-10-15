<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OnlineOrders\Form;

use Zend\Captcha;
use Zend\Filter\File\RenameUpload;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use System\Form\BaseForm;

class GroupItem extends BaseForm
{
    private $groupSelect;


    public function __construct($groupSelect)
    {
        $this->groupSelect = $groupSelect;

        parent::__construct('group_item_form');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'groupList',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Group List : ',
                'value_options' => $this->groupSelect
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
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

    public function addInputFilters()
    {


    }

}
