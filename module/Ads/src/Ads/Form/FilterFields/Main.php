<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Ads\Form\FilterFields;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class Main extends BaseForm implements InputFilterProviderInterface
{
    private $selectedFields = array();
    private $fields = array();

    public function __construct($selectedFields, $fields)
    {
        $this->fields = $fields;
        $this->selectedFields = $selectedFields;
        parent::__construct('filter_fields_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {
        $baseFieldset = new Fieldset('filters');
        $baseFieldset->add(new Type($this->selectedFields, $this->fields));
        $this->add($baseFieldset);


        $this->add(new \System\Form\Buttons('filter_fields_config'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // TODO: Implement getInputFilterSpecification() method.
        return array();
    }
}
