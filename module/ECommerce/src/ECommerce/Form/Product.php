<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace ECommerce\Form;

use ECommerce\Form\Product\Details;
use ECommerce\Form\Product\General;
use ECommerce\Form\Product\Types;
use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class Product extends BaseForm
{
    private $_categories;

    public function __construct($categories)
    {
        $this->_categories = $categories;
        parent::__construct('product');
        $this->setAttribute('class', 'normal-form');
    }

    protected function addElements()
    {
        $this->add(new General());
        $this->add(new Details($this->_categories));
        $this->add(new Types());
        $this->add(new Buttons('product'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();
    }
}
