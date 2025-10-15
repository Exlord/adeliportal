<?php

namespace OnlineOrder\Form;

use Application\API\App;
use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class Customer extends BaseForm
{
    private $pageType = '';
    private $count = '';

    public function __construct($pageType, $count)
    {
        $this->pageType = $pageType;
        $this->count = $count;
        parent::__construct('customer_form');
        $this->setAttribute('class', 'normal-form');
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));


        $this->add(array(
            'name' => 'groupId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Groups',
                'value_options' => array(
                    '1' => 'عضویت رایگان',
                    '2' => 'عضویت برنزی',
                    '3' => 'عضویت نقره ای',
                    '4' => 'عضویت طلایی',
                ),
            ),
            'attributes'=>array(

            )
        ));

        $this->add(array(
            'name' => 'amount',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Amount'
            ),
            'attributes' => array(
                'disabled' => 'disabled'
            )
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Personal Name'
            ),
            'attributes' => array(
                'class' => ''
            )
        ));

        $this->add(array(
            'name' => 'company',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Company Name'
            ),
            'attributes' => array(
                'class' => ''
            )
        ));

        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(
                'class' => ''
            )
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Phone'
            ),
            'attributes' => array(
                'class' => ''
            )
        ));

        $this->add(array(
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile'
            ),
            'attributes' => array(
                'class' => ''
            )
        ));


        $text = new Element\Textarea('address');
        $text->setLabel("Address");
        $text->setAttributes(array(
            'class' => '',
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text);

        $text1 = new Element\Textarea('comment');
        $text1->setLabel("Comment");
        $text1->setAttributes(array(
            'class' => '',
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text1);


        $text2 = new Element\Textarea('others');
        $text2->setLabel("More Description");
        $text2->setAttributes(array(
            'class' => '',
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text2);


        $this->add(array(
            'name' => 'subDomain',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Sub Domain Name',
                'description' => t('')
            ),
        ));


        $domain = new \OnlineOrder\Form\Domains($this->count);
        $this->add($domain);


        $this->add(array(
            'name' => 'publishUp',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Publish Up'
            ),
        ));

        $this->add(array(
            'name' => 'publishDown',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Publish Down'
            ),
        ));

        if ($this->pageType == 'new') {
            $image = new \Zend\Captcha\Image();
            $image->setFont(PUBLIC_PATH . '/fonts/arial.ttf');
            $image->setImgDir(PUBLIC_FILE . '/captcha');
            $image->setImgUrl(App::siteUrl() . '/clients/' . ACTIVE_SITE . '/files/captcha');
            $image->setDotNoiseLevel(5);
            $image->setWordlen(4);
            $image->setFontSize(35);
            $image->setWidth(150);
            $image->setHeight(80);
            $captcha = new Element\Captcha('captcha');
            $captcha->setCaptcha($image);
            $captcha->setAttribute('class', 'captcha');

            $this->add($captcha);
        }

        $this->add(new \System\Form\Buttons('online_order_customer'));


    }


    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        #filter by digit only
        $this->filterByDigit($filter, array(
            'mobile',
        ));

        $this->filterByTrimAndTags($filter, array(
            'company',
            'mobile',
            'phone',
            'email',
            'address',
            'comment',
            'others',
            'subDomain',
        ));


        $ownerName = $filter->get('name');
        $ownerName->setRequired(true);
        $ownerName->getFilterChain()
            ->attach(new \Zend\Filter\StringTrim())
            ->attach(new \Zend\Filter\StripTags());
        $ownerName->getValidatorChain()
            ->attach(new \Zend\Validator\StringLength(array('max' => 200)))
            ->attach(new \Zend\Validator\NotEmpty());

        $ownerName = $filter->get('mobile');
        $ownerName->setRequired(true);
        $ownerName->getValidatorChain()
            ->attach(new \Zend\Validator\StringLength(array('max' => 11)))
            ->attach(new \Zend\Validator\NotEmpty());


        $email =  $filter->get('email');
        $email
            ->setAllowEmpty(true)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress());
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());

    }


}
