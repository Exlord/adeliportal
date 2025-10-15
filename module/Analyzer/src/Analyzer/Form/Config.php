<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Analyzer\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class Config extends BaseForm
{
    public function __construct()
    {
        parent::__construct('analyzer_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAction(url('admin/configs/analyzer'));
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'count_visits',
            'options' => array(
                'label' => 'Count Visits ?',
                'description' => 'should system count the visitors',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'only_unique_visits',
            'options' => array(
                'label' => 'Only Unique Visits',
                'description' => 'if checked only the unique visits will be counted and the repeated visits of the same user during its session will be ignored',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'display_only_unique_visits',
            'options' => array(
                'label' => 'Display Only Unique Visits',
                'description' => 'if checked only the unique visits count will be displayed for users',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'multiplier',
            'options' => array(
                'label' => 'Visits Count Multiplier',
                'description' => 'the number of visitors will be multiplied with this number',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-max' => 10,
                'data-step' => 1,
                'value' => 1
            )
        ));
        $this->add(new Buttons('analyzer_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $AnalyzerFiled \Zend\InputFilter\Input
         */
//        $filter = $this->getInputFilter();
    }
}
