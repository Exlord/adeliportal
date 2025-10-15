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

class NewsLetterTemplate extends BaseForm implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('news_letter_template_form');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
            'data-cancel' => url('admin/news-letter/template')
        ));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(array(
            'name' => 'desc',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Description'
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

        $this->add(new \System\Form\Buttons('news_letter_template_form'));

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
