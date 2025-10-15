<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Application\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Widgets extends BaseForm
{
    private $widgets;

    public function __construct($widgets)
    {
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->widgets = $widgets;
        parent::__construct('system_config');
        $this->setAttribute('action', url('admin/configs/widgets'));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'widgets',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Dashboard Widgets',
                'value_options' => $this->widgets,
                'description' => 'Select witch widgets to be shown in Admin Dashboard.'
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'class' => 'select2'
            )
        ));

        $this->add(new Buttons('dashboard_widgets'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();
        $filter->get('widgets')->setAllowEmpty(true)->setRequired(false);
    }
}
