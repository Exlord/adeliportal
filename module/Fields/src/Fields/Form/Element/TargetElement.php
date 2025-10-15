<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/2/2014
 * Time: 10:44 AM
 */

namespace Fields\Form\Element;


use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class TargetElement extends Fieldset implements InputFilterProviderInterface
{
    private $inputFilters = array();

    public function __construct($inputFilters = array(), $name = null, $options = array())
    {
        $this->inputFilters = $inputFilters;
//        $this->setLabel('Target Element');
        $this->setLabelAttributes(array('class' => 'hidden'));
        $this->setAttributes(array('class' => 'collection-item'));
        parent::__construct($name, $options);
    }

    public function setInputFilterSpecification($inputFilters)
    {
        $this->inputFilters = $inputFilters;
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilters;
    }
}