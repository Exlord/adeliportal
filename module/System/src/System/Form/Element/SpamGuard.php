<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/19/14
 * Time: 10:50 AM
 */

namespace System\Form\Element;

use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;

class SpamGuard extends Element\Hidden implements InputProviderInterface
{
    protected $name = "";

    protected $validators = null;

    public function getValidator()
    {
        if (null === $this->validators) {
            $this->validators[] = new \System\Validator\SpamGuard();
        }
        return $this->validators;
    }

    /**
     * @param  null|int|string $name Optional name for the element
     * @param  array $options Optional options for the element
     * @throws \Zend\Form\Exception\InvalidArgumentException
     */
    public function __construct($name = null, $options = array())
    {
        if (empty($name)) {
            $name = "ex_s_guard";
        }
        parent::__construct($name, $options);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array(
            'name' => $this->getName(),
            'required' => false,
            'allow_empty' => true,
            'validators' => $this->getValidator(),
        );
    }
}