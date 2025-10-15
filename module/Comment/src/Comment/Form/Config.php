<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Comment\Form;

use System\Form\BaseForm;
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
        parent::__construct('comment_config');
        $this->setAttributes(array(
            'class'=> 'normal-form ajax_submit',
            'action'=> url('admin/comment/config')
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'questStatus',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Comment_config_questStatus',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes'
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'When commenting, what is the situation?',
                'value_options' => array(
                    '0' => 'Not Approved',
                    '1' => 'Approved'
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type Show Comments ?',
                'value_options' => array(
                    '0' => 'Normal',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'editTime',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'How much time is needed to allow editing comments ?',
                'value_options' => array(
                    '300' => '5 Minuets',
                    '1800' => '30 Minuets',
                    '3600' => '60 Minuets',
                    '86400' => '1 Day',
                    '0' => 'Never',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'deleteTime',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'How much time is needed to allow deleting comments ?',
                'value_options' => array(
                    '300' => '5 Minuets',
                    '1800' => '30 Minuets',
                    '3600' => '60 Minuets',
                    '86400' => '1 Day',
                    '0' => 'Never',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'count',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Number of comments displayed on the screen ?',
            ),
        ));

        $this->add(array(
            'name' => 'closedShow',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Rating for closed-show comment',
            ),
        ));

        $this->add(new \System\Form\Buttons('comment_config'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */

        $filter = $this->getInputFilter();


    }
}
