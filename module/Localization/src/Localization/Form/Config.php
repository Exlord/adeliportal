<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/16/14
 * Time: 2:14 PM
 */

namespace Localization\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\InputFilter\InputFilterProviderInterface;

class Config extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('localization_config');
        $this->setAttribute('class', 'ajax_submit');
        $this->setAction(url('admin/localization-config'));
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Select',
            'name' => 'currency',
            'options' => array(
                'label' => 'Currency',
                'value_options' => array(
                    'IRR' => 'IRR',
                    'IRT' => 'IRT'
                )
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(new Buttons('localization_config'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array();
    }
}