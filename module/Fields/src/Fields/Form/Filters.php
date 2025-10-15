<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:13 AM
 */

namespace Fields\Form;


use Fields\Form\Filters\Alnum;
use Fields\Form\Filters\Alpha;
use Fields\Form\Filters\BaseName;
use Fields\Form\Filters\Boolean;
use Fields\Form\Filters\Digits;
use Fields\Form\Filters\Dir;
use Fields\Form\Filters\HtmlEntities;
use Fields\Form\Filters\Int;
use Fields\Form\Filters\PregReplace;
use Fields\Form\Filters\StringToLower;
use Fields\Form\Filters\StringToUpper;
use Fields\Form\Filters\StringTrim;
use Fields\Form\Filters\StripNewLines;
use Fields\Form\Filters\StripTags;
use Fields\Form\Filters\UriNormalize;
use Zend\Form\Fieldset;

class Filters extends Fieldset
{
    public function __construct()
    {
        parent::__construct('filters');
        $this->setAttribute('id', 'filters');
        $this->setLabel('Filters');
        $this->setAttribute('class', 'collapsible  collapsed');

        $this->add(new Alnum());
        $this->add(new Alpha());
        $this->add(new BaseName());
        $this->add(new StringTrim());
        $this->add(new StripNewLines());
        $this->add(new StripTags());
        $this->add(new Boolean());
        $this->add(new Digits());
        $this->add(new Dir());
        $this->add(new HtmlEntities());
        $this->add(new Int());
        $this->add(new PregReplace());
        $this->add(new StringToLower());
        $this->add(new StringToUpper());
        $this->add(new UriNormalize());
    }
} 