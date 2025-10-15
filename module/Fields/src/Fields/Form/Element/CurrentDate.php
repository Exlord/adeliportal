<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Fields\Form\Element;

use Zend\Form\Element;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputProviderInterface;

class CurrentDate extends Element\Text implements InputProviderInterface
{
    private function getDate()
    {
        /* @var $dateFormat callable */
        $dateFormat = getSM('ViewHelperManager')->get('dateFormat');
        return $dateFormat(time());
    }

    public function __construct($name = null, $options = array())
    {
        $this->setAttributes(array(
            'readonly' => 'readonly',
            'class' => 'disabled'
        ));
        if(empty($this->value)){
            $this->value = $this->getDate();
        }
        parent::__construct($name, $options);
    }

    /**
     * Prepare the form element
     */
    public function prepareElement(FormInterface $form)
    {
        if(empty($this->value))
            $this->value = $this->getDate();
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
            'required' => true,
            'allow_empty' => false,
        );
    }
}
