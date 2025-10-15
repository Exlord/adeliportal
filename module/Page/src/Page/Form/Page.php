<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Page\Form;

use System\Filter\FilterHtml;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Filter\StringTrim;
use Zend\Form\Element;
use Zend\Form\Form;

class Page extends BaseForm
{
    private $tags;
    private $type;
    private $galleryArray;
    private $_domainSample;

    public function __construct($tags = null, $type = 0, $domainSample = '', $galleryArray)
    {
        $this->tags = $tags;
        $this->galleryArray = $galleryArray;
        $this->type = $type;
        $this->_domainSample = $domainSample;
        $this->setAttribute('class', 'normal-form ajax_submit');
        if ($type == 1)
            $this->setAttribute('data-cancel', url('admin/page'));
        elseif ($type == 0)
            $this->setAttribute('data-cancel', url('admin/content'));

        parent::__construct('page_form');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(array(
            'name' => 'pageTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Page Title'
            ),
            'attributes' => array(),
        ));
        if (!$this->type) {
            $this->add(array(
                'name' => 'introText',
                'type' => 'Zend\Form\Element\Textarea',
                'options' => array(
                    'label' => 'Intro Text'
                ),
                'attributes' => array(),
            ));
        }

        $this->add(array(
            'name' => 'fullText',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Page Content'
            ),
        ));

        $this->add(new PageConfig());
        $this->add(new PageImage());

        if (!$this->type) {
            $this->add(array(
                'name' => 'status',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Status',
                    'value_options' => array(
                        '1' => 'Published',
                        '2' => 'UnPublished',
                        '3' => 'Archive',
                        '4' => 'Recycle'
                    )
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));

            $this->add(array(
                'name' => 'order',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Order'
                ),
                'attributes' => array(
                    'class' => 'spinner',
                    'data-min' => -999,
                    'data-max' => 999,
                    'data-step' => 1,
                )
            ));

            $this->add(array(
                'name' => 'publishUp',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Publish Up'
                ),
                'attributes' => array(),
            ));

            $this->add(array(
                'name' => 'publishDown',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Publish Down',
                    'description' => "Page_DESC_NOT_EXPIRED",
                ),
                'attributes' => array(),
            ));

            $this->add(array(
                'name' => 'published',
                'type' => 'Zend\Form\Element\Checkbox',
                'options' => array(
                    'label' => 'Published To Front Page ?'
                ),
            ));


            $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'attributes' => array(
                    'multiple' => 'multiple',
                    'size' => 10,
                    'class' => 'select2',
                ),
                'name' => 'tags',
                'options' => array(
                    'disable_inarray_validator' => true,
                    'label' => 'Tags',
                    'value_options' => $this->tags,
                ),
            ));

            $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'attributes' => array(
                    'multiple' => 'multiple',
                    'size' => 10,
                    'class' => 'select2',
                ),
                'name' => 'refGallery',
                'options' => array(
                    'disable_inarray_validator' => true,
                    'label' => 'Gallery',
                    'value_options' => $this->galleryArray,
                ),
            ));

        } else {
            $this->add(array(
                'name' => 'status',
                'type' => 'Zend\Form\Element\Checkbox',
                'options' => array(
                    'label' => 'Is Enabled ?'
                ),
            ));
        }

        if (getSM()->has('domain_table')) {
            $domains = getSM('domain_table')->getArray();
            if (count($domains) > 1) {
                $this->add(array(
                    'name' => 'domains',
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                        'class' => 'select2',
                        'multiple' => 'multiple',
                    ),
                    'options' => array(
                        'label' => 'Display in Domains',
                        'description' => "this content will only be displayed in the selected domains<br/> none selected == All",
                        'value_options' => $domains,
                        'disable_inarray_validator' => true
                    )
                ));
            }
        }

        if (getSM()->has('language_table')) {
            $langs = getSM('language_table')->getArray(true);
            if (count($langs) > 1) {
                $this->add(array(
                    'name' => 'languages',
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                        'class' => 'select2',
                        'multiple' => 'multiple',
                    ),
                    'options' => array(
                        'label' => 'Available Languages',
                        'description' => "this content will only be available in the selected languages<br/> none selected == All",
                        'value_options' => $langs,
                        'disable_inarray_validator' => true
                    )
                ));
            }
        }

        $this->add(new Buttons('page'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        if ($this->has('refGallery')) {
            $this->setRequiredFalse($filter, array(
                'refGallery',
            ));
        }

        if (!$this->type) {
            $this->setRequiredFalse($filter->get('config'), array(
                'viewAuthor',
                'commentStatus',
                'showCommentSection',
                'showDate',
                'showHits',
                'showTags',
            ));

        }

        // File Input
        $filter->get('image')->get('image')->setRequired(false);
        $filter->get('image')->get('image')->getValidatorChain()
            ->attachByName('filesize', array('max' => 20480))
            ->attachByName('filemimetype', array('mimeType' => 'image/png,image/x-png,image/jpg,image/gif'))
            ->attachByName('fileimagesize', array('maxWidth' => 2000, 'maxHeight' => 2000));

        // $filter->get('tags')->setAllowEmpty(true);
        // $filter->get('tags')->setRequired(false);

        //domains
        //languages
        if ($filter->has('domains')) {
            $filter->get('domains')->setRequired(false)->setAllowEmpty(true);
        }
        if ($filter->has('languages')) {
            $filter->get('languages')->setRequired(false)->setAllowEmpty(true);
        }

        if (!$this->type)
            $filter->get('introText')->getFilterChain()->attach(new StringTrim())->attach(new FilterHtml());

        $filter->get('fullText')->getFilterChain()->attach(new StringTrim())->attach(new FilterHtml());
    }
}
