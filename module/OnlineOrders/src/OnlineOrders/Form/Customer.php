<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OnlineOrders\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;

class Customer extends BaseForm
{
    private $idGroup;
    private $sumPrice;
    private $sumPishFact;

    public function __construct($idGroup,$sumPrice,$sumPishFact)
    {
        $this->idGroup = $idGroup;
        $this->sumPrice = $sumPrice;
        $this->sumPishFact = $sumPishFact;
        parent::__construct('customer_form');
        $this->setAttributes(array(
            'class', 'normal-form',
            'action'=>url('admin/online-orders')
        ));
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'idGroup',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => $this->idGroup
            ),
        ));

        $this->add(array(
            'name' => 'confirmation',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'itemCustomer',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                'label' => 'itemCustomer',
                'description' => 'itemCustomer'
            ),
            'attributes'=>array(
                'class'=>'itemCustomer'
            )
        ));

        $this->add(array(
            'name' => 'langCustomer',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                'label' => 'langCustomer',
                'description' => 'langCustomer'
            ),
            'attributes'=>array(
                'class'=>'langCustomer'
            )
        ));


        $this->add(array(
            'name' => 'supportPer',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Technical support and updates to the site by experts to participate'
            ),
        ));


        $this->add(array(
            'name' => 'namePer',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Personal Name'
            ),
            'attributes'=>array(
                'class'=>'el-per-txt'
            )
        ));

        $this->add(array(
            'name' => 'nameCompanyPer',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Company Name'
            ),
            'attributes'=>array(
                'class'=>'el-per-txt'
            )
        ));

        $this->add(array(
            'name' => 'emailPer',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes'=>array(
                'class'=>'el-per-txt'
            )
        ));

        $this->add(array(
            'name' => 'phonePer',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Phone'
            ),
            'attributes'=>array(
                'class'=>'el-per-txt'
            )
        ));

        $this->add(array(
            'name' => 'mobilePer',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile'
            ),
            'attributes'=>array(
                'class'=>'el-per-txt'
            )
        ));


        $text = new Element\Textarea('addressPer');
        $text->setLabel("Address");
        $text->setAttributes(array(
            'class'=>'el-per-txt-area',
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text);

        $text1 = new Element\Textarea('commentPer');
        $text1->setLabel("Comment");
        $text1->setAttributes(array(
            'class'=>'el-per-txt-area',
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text1);


        $text2 = new Element\Textarea('others');
        $text2->setLabel("Other Facilities");
        $text2->setAttributes(array(
            'class'=>'el-per-txt-area',
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text2);




        $this->add(array(
            'name' => 'date',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => date("Ymd"),
            )
        ));


        $this->add(array(
            'name' => 'typePayment',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'typePayment'
            ),
        ));


        $this->add(array(
            'name' => 'infoPayment',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'infoPayment'
            ),
            'attributes'=>array(
                'rows'=> '4',
                'cols'=> '60',
            )
        ));



        $this->add(array(
            'name' => 'end4CardNumber',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'end4CardNumber'
            ),
        ));

        $this->add(array(
            'name' => 'datePayment',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'datePayment'
            ),
        ));

        $this->add(array(
            'name' => 'seryalPayment',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'seryalPayment'
            ),
        ));


        $this->add(array(
            'name' => 'factorNumber',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'factorNumber'
            ),
        ));


        $this->add(array(
            'name' => 'refCode',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'refCode'
            ),
            'attributes'=>array(
                'rows'=> '4',
                'cols'=> '60',
            )
        ));





        $this->add(array(
            'name' => 'resultPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Total cost of Items'
            ),
            'attributes' => array(
                'value' => $this->sumPrice,
                'id' => 'resultPrice',
                'class'=> 'inpt-price',
            ),
        ));

        $this->add(array(
            'name' => 'resultPriceLang',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Total price languages'
            ),
            'attributes' => array(
                'value' => '0',
                'id' => 'resultPriceLang',
                'class'=> 'inpt-price',
            ),
        ));

        $this->add(array(
            'name' => 'sumResultPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'The total price'
            ),
            'attributes' => array(
                'value' => $this->sumPishFact,
                'id' => 'sumResultPrice',
                'class'=> 'inpt-price',
            ),
        ));

        $this->add(array(
            'name' => 'domainName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Domain Name'
            ),
        ));

        $this->add(array(
            'name' => 'domainType',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Domain Type'
            ),
            'attributes'=>array(
                'class'=>'domainType'
            )
        ));

       /* $image = new \Zend\Captcha\Image();
        $image->setFont(PUBLIC_PATH . '/fonts/arial.ttf');
        $image->setImgDir(PUBLIC_FILE . '/captcha');
        $image->setImgUrl(siteUrl() . '/clients/' . ACTIVE_SITE . '/files/captcha');
        $image->setDotNoiseLevel(5);
        $image->setWordlen(4);
        $image->setFontSize(35);
        $image->setWidth(150);
        $image->setHeight(80);
        $captcha = new Element\Captcha('captcha');
        $captcha->setCaptcha($image);
        $captcha->setAttribute('class', 'captcha');

        $this->add($captcha);*/

        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_customer_form'
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-create',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'button-submit-form',
            )
        ));


    }


    protected function addInputFilters()
    {
        /**
         * @var $sampleFiled \Zend\InputFilter\Input
         */


    }



}
