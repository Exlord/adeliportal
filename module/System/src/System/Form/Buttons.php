<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace System\Form;


class Buttons extends \Zend\Form\Fieldset
{
    const SAVE = 'submit';
    const SAVE_NEW = 'submit-new';
    const SAVE_CLOSE = 'submit-close';
    const SAVE_AS_COPY = 'submit-copy';
    const CANCEL = 'cancel';
    const CSRF = 'csrf';
    const SPAM = 'spam';

    public function __construct($name = 'default', $items = null)
    {
        parent::__construct('buttons');
        $this->setLabel('Buttons');
//        $this->setAttribute('class', 'btn-group');
        $this->setLabelAttributes(array('class' => 'hidden'));
//        $this->setOption('column-size', 'xs-12');
//        $this->setAttribute('class', 'col-xs-12 buttons-list');
        $this->setOption('twb-layout', 'inline');

        if ($items == null) {
            $items = array(self::SAVE, self::SAVE_NEW, self::CANCEL, self::CSRF, self::SPAM);
        }

        if (in_array(self::CSRF, $items)) {
            $this->add(array(
                'type' => 'Zend\Form\Element\Csrf',
                'name' => 'csrf__' . $name,
                'options' => array(
                    'csrf_options' => array(
                        'timeout' => 600
                    )
                )
            ));
        }

        if (in_array(self::SPAM, $items)) {
            $this->add(array(
                'type' => 'System\Form\Element\SpamGuard',
            ));
        }

        if (in_array(self::SAVE, $items)) {
            $this->add(array(
                'type' => 'Zend\Form\Element\Button',
                'name' => 'submit',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Save',
                    'class' => 'btn btn-success',
                ),
                'options' => array(
                    'label' => 'Save',
                    'glyphicon' => 'edit',
//                    'twb-layout' => 'inline'
                )
            ));
        }

        if (in_array(self::SAVE_NEW, $items)) {
            $this->add(array(
                'type' => 'Zend\Form\Element\Button',
                'name' => 'submit-new',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Save and New',
                    'class' => 'btn btn-success',
                ),
                'options' => array(
                    'label' => 'Save and New',
                    'glyphicon' => 'edit',
//                    'twb-layout' => 'inline'
                )
            ));
        }

        if (in_array(self::SAVE_CLOSE, $items)) {
            $this->add(array(
                'type' => 'Zend\Form\Element\Button',
                'name' => 'submit-close',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Save and Close',
                    'class' => 'btn btn-default',
                ),
                'options' => array(
                    'label' => 'Save and Close',
                    'glyphicon' => 'edit',
//                    'twb-layout' => 'inline'
                )
            ));
        }

        if (in_array(self::SAVE_AS_COPY, $items)) {
            $this->add(array(
                'type' => 'Zend\Form\Element\Button',
                'name' => 'submit-copy',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Save As Copy',
                    'class' => 'btn btn-default',
                ),
                'options' => array(
                    'label' => 'Save As Copy',
                    'glyphicon' => 'plus',
//                    'twb-layout' => 'inline'
                )
            ));
        }

        if (in_array(self::CANCEL, $items)) {
            $this->add(array(
                'name' => 'cancel',
                'type' => 'Zend\Form\Element\Button',
                'attributes' => array(
                    'type' => 'submit',
                    'value' => 'Cancel',
                    'class' => 'btn btn-default',
                ),
                'options' => array(
                    'label' => 'Cancel',
                    'glyphicon' => 'minus-sign',
//                    'twb-layout' => 'inline'
                )
            ));
        }
    }
}
