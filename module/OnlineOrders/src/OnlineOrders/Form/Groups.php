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

class Groups extends BaseForm
{
    private $groupSelect;
    public function __construct($groupSelect)
    {
        $this->groupSelect = $groupSelect;
        parent::__construct('groups_form');
        $this->setAttributes(array(
            'class', 'normal-form',
            'action'=>url('admin/online-orders/group-select')
        ));
    }
    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(array(
            'name' => 'groupName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Group Name'
            ),
        ));

        $text = new Element\Textarea('groupDesc');
        $text->setLabel("Description");
        $text->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text);

        $this->add(array(
            'name' => 'groupPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Price'
            ),
        ));


         $this->add(array(
             'name' => 'groupPosition',
             'type' => 'Zend\Form\Element\Text',
             'options' => array(
                 'label' => 'Position'
             ),
         ));

        $this->add(array(
            'name' => 'groupParentId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Categories',
                'value_options' => $this->groupSelect
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
        'type' => 'Zend\Form\Element\Checkbox',
        'name' => 'groupPermit',
        'options' => array(
            'label' => 'Show On The WebSite',
        ),
        'attributes'=>array(
            'class'=>'groupCheck',
        )
    ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'groupShowLang',
            'options' => array(
                'label' => 'Show Language On The WebSite',
            ),
            'attributes'=>array(
                'class'=>'groupCheck',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'groupShowSupport',
            'options' => array(
                'label' => 'Show Support On The WebSite',
            ),
            'attributes'=>array(
                'class'=>'groupCheck',
            )
        ));

        $this->add(array(
            'name' => 'user-file',
            'type' => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'Choose an image',
            ),

        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_groups_form'
        ));


        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-create',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'button',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-edit',
            'attributes' => array(
                'value' => 'Edit',
                'class' => 'button',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-delete',
            'attributes' => array(
                'value' => 'Delete',
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
