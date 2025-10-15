<?php
namespace NewsLetter\Form;

use System\Filter\FilterHtml;
use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;
use Zend\InputFilter\InputFilterProviderInterface;

class NewsLetter extends BaseForm implements InputFilterProviderInterface
{

    private $roles = array();
    private $configMail = array();

    public function __construct($roles, $configMail)
    {

        $this->roles = $roles;
        $this->configMail = $configMail;
        parent::__construct('news_letter_form');
        $this->setAttributes(array(
            'class' => 'normal-form',
        ));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'from',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'From'
            ),
            'attributes' => array(
                'value' => $this->configMail
            )
        ));

        $this->add(array(
            'name' => 'roles',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Roles',
                'value_options' => $this->roles,
                'description' => t('If the user selected. The emails will be sent as default user roles')
            ),
            'attributes' => array(
                'class' => 'group-list-item select2',
            ),
        ));

        $this->add(array(
            'name' => 'to',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'To',
                'description' => t('With, be separated')
            ),
            'attributes' => array(
                'cols' => '100',
                'rows' => '4',
            )
        ));

        $this->add(array(
            'name' => 'subject',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Subject'
            ),
        ));

        $this->add(array(
            'name' => 'body',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Body'
            ),
            'attributes' => array(
                'cols' => '100',
                'rows' => '4',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'saveTemplate',
            'options' => array(
                'label' => 'Do you want to save the form made​​?',
            ),
        ));


        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_news_letter_form'
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-Send',
            'attributes' => array(
                'value' => 'Send',
                'class' => 'button',
            )
        ));

        /* $this->add(array(
             'type' => 'submit',
             'name' => 'submit-cancel',
             'attributes' => array(
                 'value' => 'Cancel',
                 'class' => 'button',
             )
         ));*/


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
            'body' => array(
                'name' => 'body',
                'filters' => array(
                    new Filter\StringTrim(),
                    new FilterHtml()
                )
            ),
        );
    }
}
