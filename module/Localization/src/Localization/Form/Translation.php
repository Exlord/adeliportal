<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 6/10/14
 * Time: 12:42 PM
 */

namespace Localization\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\InputFilter\InputFilterProviderInterface;

class Translation extends BaseForm implements InputFilterProviderInterface
{
    private $langs;

    public function __construct($langs)
    {
        $this->langs = $langs;
        parent::__construct('miscellaneous_translation');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', url('admin/translate-miscellaneous'));
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Textarea',
            'name' => 'default',
            'options' => array(
                'label' => 'Default',
                'description' => 'this value will be used if there is no translation available'
            )
        ));

        foreach ($this->langs as $lSign => $lName) {
            $this->add(array(
                'type' => 'Textarea',
                'name' => $lSign,
                'options' => array(
                    'label' => $lName
                )
            ));
        }

        $this->add(new Buttons('miscellaneous_translation'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $_inputs = array(
            'filters' => array(
                array('name' => 'StringTrim'),
                array('name' => 'StripTags'),
            )
        );
        $input = array(
            'default' => array(
                'required' => true,
                'allow_empty' => false,
                'filters' => array(
                    array('name' => 'StringTrim'),
                    array('name' => 'StripTags'),
                )
            )
        );
        foreach ($this->langs as $lSign => $lName) {
            $input[$lSign] = $_inputs;
        }
        return $input;
    }
}