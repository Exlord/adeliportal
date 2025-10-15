<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace RealEstate\Form;

use Zend\Captcha;
use Zend\Filter\File\RenameUpload;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use System\Form\BaseForm;

class SmsText extends BaseForm
{


    public function __construct()
    {
        parent::__construct('smstext_form');
        $this->setAttribute('class', 'normal-form');
        $this->setAttribute('action',url('admin/real-estate/sms-text'));
    }

    protected function addElements()
    {
        $text= new Element\Textarea('text1');
        $text->setLabel("your message for Confirmation home :");
        $text->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));


        $text1= new Element\Textarea('text2');
        $text1->setLabel("your message for Confirmation moshaver :");
        $text1->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));

        $text2= new Element\Textarea('text3');
        $text2->setLabel("your message for not Confirmation moshaver :");
        $text2->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));

        $text3= new Element\Textarea('text4');
        $text3->setLabel("your message for Expiration home :");
        $text3->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));

        $text4= new Element\Textarea('text5');
        $text4->setLabel("your message After sending the details to the buyer :");
        $text4->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));

        $this->add($text);
        $this->add($text1);
        $this->add($text2);
        $this->add($text3);
        $this->add($text4);

        $this->add(new \System\Form\Buttons($this->getName('smstext_form')));
    }

    public function addInputFilters()
    {
        $filter = $this->getInputFilter();
    }


}
