<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ajami
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Note\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class Note extends BaseForm implements InputFilterProviderInterface
{

    public function __construct()
    {
//        $this->setAttribute('class', 'ajax_submit');
        parent::__construct('note_form');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Hidden',
            'name' => 'id'
        ));

        $this->add(array(
            'type' => 'Hidden',
            'name' => 'entityId'
        ));

        $this->add(array(
            'type' => 'Hidden',
            'name' => 'entityType'
        ));

        $this->add(array(
            'name' => 'note',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'NOTE'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'type' => 'MultiCheckbox',
            'name' => 'visibility',
            'options' => array(
                'label' => 'NOTE_VISIBILITY',
                'value_options' => \Note\API\Note::$visibility,
                'inline' => false,
            ),
            'attributes' => array()
        ));

        $this->add(new Buttons('note_form', array(Buttons::SAVE, Buttons::SPAM)));
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
