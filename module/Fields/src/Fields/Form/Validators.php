<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:13 AM
 */

namespace Fields\Form;


use Fields\Form\Validators\Alnum;
use Fields\Form\Validators\Alpha;
use Fields\Form\Validators\Between;
use Fields\Form\Validators\Date;
use Fields\Form\Validators\Digits;
use Fields\Form\Validators\EmailAddress;
use Fields\Form\Validators\ExcludeExtension;
use Fields\Form\Validators\ExcludeMimeType;
use Fields\Form\Validators\Extension;
use Fields\Form\Validators\Float;
use Fields\Form\Validators\GreaterThan;
use Fields\Form\Validators\Hex;
use Fields\Form\Validators\Hostname;
use Fields\Form\Validators\ImageSize;
use Fields\Form\Validators\Int;
use Fields\Form\Validators\Ip;
use Fields\Form\Validators\IsCompressed;
use Fields\Form\Validators\IsImage;
use Fields\Form\Validators\LessThan;
use Fields\Form\Validators\MimeType;
use Fields\Form\Validators\Regex;
use Fields\Form\Validators\Size;
use Fields\Form\Validators\Step;
use Fields\Form\Validators\StringLength;
use Fields\Form\Validators\UploadFile;
use Fields\Form\Validators\WordCount;
use Zend\Form\Fieldset;

class Validators extends Fieldset
{
    public function __construct()
    {
        parent::__construct('validators');
        $this->setAttribute('id', 'validators');
        $this->setLabel('Validators');
        $this->setAttribute('class', 'collapsible  collapsed');

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'required',
            'options' => array(
                'label' => 'Required',
                'description' => 'Is this fields value required ?'
            )
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'allow_empty',
            'options' => array(
                'label' => "Don't Allow Empty",
                'description' => 'Can this fields value be empty ?'
            )
        ));
        $this->add(new Alnum());
        $this->add(new Alpha());
        $this->add(new Between());
        $this->add(new ExcludeExtension());
        $this->add(new ExcludeMimeType());
        $this->add(new Extension());
        $this->add(new ImageSize());
        $this->add(new IsCompressed());
        $this->add(new IsImage());
        $this->add(new MimeType());
        $this->add(new Size());
        $this->add(new UploadFile());
        $this->add(new Date());
        $this->add(new Digits());
        $this->add(new Float());
        $this->add(new Int());
        $this->add(new EmailAddress());
        $this->add(new WordCount());
        $this->add(new GreaterThan());
        $this->add(new Hex());
        $this->add(new Hostname());
        $this->add(new Ip());
        $this->add(new LessThan());
        $this->add(new Regex());
        $this->add(new Step());
        $this->add(new StringLength());
    }
} 