<?php

namespace Contact\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;

class ContactUser extends BaseForm
{
    private $category = array();
    private $count = 1;

    public function __construct($category, $count = 1)
    {
        $this->category = $category;
        $this->count = $count;
        parent::__construct('contact_user_form');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
            'data-cancel' => url('admin/contact/user')
        ));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'showEmail',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'CONTACT_SHOW_EMAIL'
            ),
        ));


        $this->add(array(
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'smsNumber',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'CONTACT_SMS_NUMBER'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Phone'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'fax',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Fax'
            ),
            'attributes' => array(),
        ));


        $this->add(array(
            'name' => 'address',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Address'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Description'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'role',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Role'
            ),
            'attributes' => array(),
        ));

        $this->add(array(
            'name' => 'google',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
                'value_options' => array(
                    '0' => 'Not Approved',
                    '1' => 'Approved'
                ),
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Contact_FORM_TYPE',
                'value_options' => array(
                    '0' => 'Contact Us',
                    '1' => 'Representative',
                    '2' => 'All'
                ),
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'catId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Categories',
                'value_options' => $this->category,
                'description' => 'contact_user_form_desc_needCategoryWithMachineName',
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $select = new \Contact\Form\ConfigSelect($this->count);
        $this->add($select);


        $this->add(new Buttons('contact_user'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        /* $this->setRequiredFalse($filter->get('page_config'),array(
            'viewAuthor',
             'commentStatus'
         ));*/

        // $filter->get('tags')->setAllowEmpty(true);
        // $filter->get('tags')->setRequired(false);
    }
}
