<?php
namespace OrgChart\Form;

use System\Form\BaseForm;

class Config extends BaseForm
{
    public function __construct()
    {
        parent::__construct('chart_config');
        $this->setAttributes(array(
            'action' => url('admin/org-chart/config'),
            'class' => 'normal-form ajax_submit',
        ));
    }

    public function addElements()
    {

        $this->add(array(
            'name' => 'viewTypeNode',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'OrgChart_VIEW_TYPE_NODE',
                'value_options' => array(
                    1 => 'OrgChart_OTHER_PAGE',
                    2 => 'OrgChart_POPUP',
                    3 => 'OrgChart_SLIDE',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(new \System\Form\Buttons('chart_config'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();
    }


}
