<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace RSS\Form;

use RSS\Model\ReaderTable;
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

class Reader extends BaseForm
{
    public function __construct()
    {
        parent::__construct('rss_reader');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel',url('admin/rss-reader'));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Title'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'url',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Url'
            ),
            'attributes' => array(
                'size' => 100,
                'class' => 'left-align'
            )
        ));
        $this->add(array(
            'name' => 'readInterval',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Read Interval',
                'description' => 'How often this feeds content should be updated ?',
                'value_options' => ReaderTable::$readInterval
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));
        $this->add(array(
            'name' => 'feedLimit',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Feed Limit',
                'description' => 'default=10, max=100'
            ),
        ));


        $this->add(new Buttons('rss_reader'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */
        $filter = $this->getInputFilter();
    }
}
