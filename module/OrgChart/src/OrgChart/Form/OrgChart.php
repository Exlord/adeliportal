<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OrgChart\Form;

use OrgChart\Form\Config\Config;
use OrgChart\Form\Config\Fields;
use OrgChart\Form\Config\Roles;
use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;


class OrgChart extends BaseForm
{
    private $fields;

    public function __construct($fields_list)
    {
        $this->fields = $fields_list;
        parent::__construct('org_chart');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
            'method' => 'post'
        ));
    }

    public function addElements()
    {
        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Description'
            ),
            'required' => false,
            'validators' => array(
                array('name' => 'string_length', 'options' => array('min' => 5, 'max' => 2000),),
            ),
            'filters' => array(
                array('name' => 'StringTrim'),
            ),
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
                'value_options' => array(
                    '0' => 'Not Approved',
                    '1' => 'Approved'
                ),
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(new Config($this->fields));

        $this->add(new \System\Form\Buttons('org_chart_form'));
    }

    public function addInputFilters()
    {
        $filter = $this->getInputFilter();
    }
}
