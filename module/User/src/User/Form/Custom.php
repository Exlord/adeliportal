<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/3/13
 * Time: 9:26 AM
 */

namespace User\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\InputFilter\InputFilterProviderInterface;

class Custom extends BaseForm implements InputFilterProviderInterface
{

    public function __construct()
    {
        $this->setAttribute('class', 'normal-form ajax_submit');
        parent::__construct('user_custom_details');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'System\Form\Fieldset',
            'name' => 'profile2',
            'options' => array(
                'label' => 'Custom Details'
            )
        ));
        $this->add(new Buttons('user_custom_details'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilters;
    }
}