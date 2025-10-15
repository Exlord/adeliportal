<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/6/2014
 * Time: 12:52 PM
 */

namespace System\Form;


use Zend\InputFilter\InputFilterProviderInterface;

class Fieldset extends \Zend\Form\Fieldset implements InputFilterProviderInterface
{
    private $inputFilters = array();

    public function setInputFiltersConfig(array $inputFilters)
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