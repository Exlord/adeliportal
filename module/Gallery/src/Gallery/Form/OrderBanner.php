<?php

namespace Gallery\Form;

use Application\API\App;
use System\Captcha\CaptchaFactory;
use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Filter;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class OrderBanner extends BaseForm
{

    private $position = array();
    private $type = '';

    public function __construct($position, $type = 'new')
    {
        $this->position = $position;
        $this->type = $type;
        parent::__construct('order_banner_form');
        $this->setAttributes(array(
            'class' => 'normal-form',
            'action' => url('app/order-banner'),
        ));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'position',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'Position',
                'value_options' => $this->position,
                'description' => ''
            )
        ));


        $this->add(array(
            'name' => 'countMonth',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Count Month ?',
                'value_options' => array(
                    '1' => '1 Month',
                    '2' => '2 Month',
                    '3' => '3 Month',
                    '4' => '4 Month',
                    '5' => '5 Month',
                    '6' => '6 Month',
                    '7' => '7 Month',
                    '8' => '8 Month',
                    '9' => '9 Month',
                    '10' => '10 Month',
                    '11' => '11 Month',
                    '12' => '12 Month',
                ),
            ),
            'attributes'=>array(

            )
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name'
            ),
        ));

        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Title'
            ),
            'attributes' => array(
                'class' => ''
            )
        ));

        $this->add(array(
            'name' => 'url',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Url Address'
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
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile'
            ),
            'attributes' => array(
                'class' => ''
            )
        ));


        $text2 = new Element\Textarea('description');
        $text2->setLabel("More Description");
        $text2->setAttributes(array(
            'class' => '',
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text2);


        $bannerImageBox = new \Gallery\Form\BannerImageBox();
        $this->add($bannerImageBox);

        if ($this->type == 'new') {
//            $image = new \Zend\Captcha\Image();
//            $image->setFont(PUBLIC_PATH . '/fonts/arial.ttf');
//            $image->setImgDir(PUBLIC_FILE . '/captcha');
//            $image->setImgUrl(App::siteUrl() . '/clients/' . ACTIVE_SITE . '/files/captcha');
//            $image->setDotNoiseLevel(5);
//            $image->setWordlen(4);
//            $image->setFontSize(35);
//            $image->setWidth(150);
//            $image->setHeight(80);
//            $captcha = new Element\Captcha('captcha');
//            $captcha->setCaptcha($image);
//            $captcha->setAttribute('class', 'captcha');

            $this->add(CaptchaFactory::create());
        }

        $this->add(new \System\Form\Buttons('order_banner'));


    }


    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        #filter by digit only
        $this->filterByDigit($filter, array(
            'mobile',
        ));

        $this->filterByTrimAndTags($filter, array(
            'name',
            'mobile',
            'email',
            'url',
            'position',
            'countMonth',
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


        $email = $filter->get('email');
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
