<?php
namespace Page\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Config extends BaseForm
{
    private $tags = array();

    public function __construct($tags)
    {
        $this->tags = $tags;
        $this->setAttribute('class', 'normal-form ajax_submit');
        parent::__construct('system_config');
        $this->setAttribute('action', url('admin/page-config'));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'viewAuthor',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_AUTHOR_ON_PAGE',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));



        $this->add(array(
            'name' => 'showTags',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_TAGS_ON_PAGE',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'PAGE_LOW_INFO',
                    '2' => 'PAGE_ALL_INFO',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'showDate',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_DATE_ON_PAGE',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'showHits',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_HITS_ON_PAGE',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'showCommentSection',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_COMMENT_ON_PAGE',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'name' => 'showRateSection',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'PAGE_SHOW_RATE_ON_PAGE',
                'value_options' => array(
                    '0' => 'No',
                    '1' => 'Yes',
                ),
            ),
            'attributes' => array(
                'class' => 'select2'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'multiple' => 'multiple',
                'size' => 10,
                'class' => 'select2',
            ),
            'name' => 'pageTags',
            'options' => array(
                'disable_inarray_validator' => true,
                'label' => 'Tags',
                'value_options' => $this->tags,
            ),
        ));

        $this->add(new \System\Form\Buttons('system_config'));
    }

    protected function addInputFilters()
    {
        // TODO: Implement addInputFilters() method.
    }
}
