<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Ads\Form\SelectFields;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class SelectFields extends BaseForm implements InputFilterProviderInterface
{
    private $fields_Array = array();

    public function __construct($fields_Array)
    {
        $this->fields_Array = $fields_Array;
        parent::__construct('select_fields_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'showFields',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $this->fields_Array,
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'id'=>'showFields',
            )
        ));

        $this->add(array(
            'name' => 'selectFields',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $this->fields_Array,
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'id'=>'selectFields'
            )
        ));



        $this->add(new \System\Form\Buttons('select_fields_config'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $input['showFields'] = array(
            'name' => 'showFields',
            'required' => false,
            'allow_empty' => true,
        );
        return $input;
    }
}
