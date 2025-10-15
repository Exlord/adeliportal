<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/20/14
 * Time: 11:20 AM
 */

namespace User\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use User\Form\Config\FieldsAccess;
use Zend\InputFilter\InputFilterProviderInterface;

class UserConfig extends BaseForm implements InputFilterProviderInterface
{
    private $fields;

    public function __construct($fields)
    {
        $this->fields = $fields;
        parent::__construct('user_config');
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {
        $this->add(new FieldsAccess($this->fields));
        $this->add(new Buttons('user_config'));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array();
    }
}