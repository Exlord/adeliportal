<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OrgChart\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;


class ChartNode extends BaseForm
{
    private $charts = array();
    private $user = array();
    private $node = array();

    public function __construct($userId, $parentNode, $charts)
    {
        $this->charts = $charts;
        $this->user = $userId;
        $this->node = $parentNode;
        parent::__construct('chart_node');
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
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Title'
            ),
        ));

        $this->add(array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'User',
                'value_options' => $this->user
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'chartId',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'class' => 'select2 select-chart-id',
            ),
            'options' => array(
                'label' => 'OrgChart_CHART',
                'description' => "",
                'value_options' => $this->charts
            )
        ));

        $this->add(array(
            'name' => 'parentId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Parent Node',
                'value_options' => $this->node
            ),
            'attributes' => array(
                'class' => 'select2 select-parent-node',
            )
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

        $this->add(new \System\Form\Buttons('org_chart_form'));
    }

    public function addInputFilters()
    {
        $filter = $this->getInputFilter();
        $this->setRequiredFalse($filter,array(
            'parentId',
        ));
    }
}
