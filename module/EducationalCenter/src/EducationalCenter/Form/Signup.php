<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/30/14
 * Time: 10:54 AM
 */

namespace EducationalCenter\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class Signup extends BaseForm implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('signup');
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {

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