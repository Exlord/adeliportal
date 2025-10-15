<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace FormsManager\Form;

use System\Filter\FilterHtml;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\ModuleManager\Feature\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class DynamicForm extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('dynamic_form');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', url('admin/forms'));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Title'
            ),
        ));
//        $this->add(array(
//            'name' => 'formType',
//            'type' => 'Zend\Form\Element\Radio',
//            'options' => array(
//                'label' => 'Form Type',
//                'value_options' => \FormsManager\API\Form::$FormTypes,
//                'value' => \FormsManager\API\Form::CUSTOM_FORM
//            ),
//            'attributes' => array(
//                'class' => 'hidden'
//            )
//        ));
//        $this->add(array(
//            'name' => 'templateFile',
//            'type' => 'Zend\Form\Element\Select',
//            'options' => array(
//                'label' => 'Template',
//                'value_options' => \FormsManager\API\Form::getAvailableTemplates(),
//            ),
//            'attributes' => array(
//                'class' => 'hidden',
//                'data-show-on' => 'formType_1'
//            )
//        ));

        $this->add(array(
            'name' => 'format',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Template'
            ),
            'attributes' => array(
//                'class' => 'hidden',
                'data-show-on' => 'formType_2',
                'id' => 'format'
            )
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Sent To Email',
                'description' => 'If email is provided whenever this form is filled a copy will be send to this email'
            ),
            'attributes' => array(
                'size' => 60
            )
        ));

        $this->add(array(
            'name' => 'editable',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Editable',
                'description' => 'If unchecked , this forms data will not be editable once it is submitted. In this case the id or any unique value in the form will be regenerated.'
            ),
            'attributes' => array()
        ));

        $this->add(new FormConfig());

        $this->add(new Buttons('DynamicForm'));
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getInputFilterConfig()
    {
        return array(
            'title' => array(
                'name' => 'title',
                'required' => true,
            ),
            'format' => array(
                'name' => 'format',
                'filters' => array(
                    new Filter\StringTrim(),
                    new FilterHtml()
                )
            ),
        );
    }
}
