<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace GeographicalAreas\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;

class Area extends BaseForm
{
    private $parentId = array();

    public function __construct($parentId)
    {
        $this->parentId = $parentId;
        $this->setAttribute('method', 'post')
            ->setAttribute('class', 'normal-form ajax_submit');
           // ->setHydrator(new ClassMethodsHydrator(false))
           // ->setInputFilter(new InputFilter());
        parent::__construct('area_form');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'areaTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Title'
            ),
        ));

        $this->add(array(
            'name' => 'parentId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Category Item Parent',
                'value_options' => $this->parentId,
                'empty_option' => '-- Select --',
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));


        $this->add(array(
            'name' => 'itemStatus',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Is Enabled ?'
            ),
        ));
        $this->add(array(
            'name' => 'itemOrder',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Order',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => -999,
                'data-max' => 999,
                'data-step' => 1,
            )
        ));
        $this->add(array(
            'name' => 'cityId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'City',
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));
        $this->add(array(
            'type' => 'System\Form\Buttons',
            'name' => 'buttons'
        ));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();
        $parentId = $filter->get('parentId');
        $parentId->setRequired(false);
    }


}
