<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace PM\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
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

class PM extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('pm');
        $this->setAction(url('admin/pm/new'));
        $this->setAttribute('data-cancel', url('admin/pm'));
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {
        $to = array(
            'name' => 'to',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'To User'
            ),
            'attributes' => array(
                'placeholder' => 'Username'
            )
        );
        if (isAllowed(\PM\API\PM::SEND_TO_MULTIPLE))
            $to['attributes']['multiple'] = 'multiple';

        $this->add($to);

        $this->add(array(
            'name' => 'msg',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Message'
            ),
            'attributes' => array(
                'cols' => 25,
                'rows' => 5,
                'placeholder' => 'Message',
                'id' => 'pm_msg'
            )
        ));

        $this->add(new Buttons('PM', array(Buttons::SAVE, Buttons::CANCEL, Buttons::SAVE_NEW, Buttons::SPAM, Buttons::CSRF)));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'msg' => array(
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim')
                )
            )
        );
    }
}
