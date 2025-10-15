<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace File\Form;

use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class FileCollection extends Element\Collection
{
    /**
     * @param null $id
     * @param int $count
     * @param $target_element \File\Form\FileField
     * @param bool $should_create_template
     * @param bool $allow_add
     */
    public function __construct($id, $count = 1, $target_element, $should_create_template = true, $allow_add = true)
    {
        parent::__construct($id);
        $this->setOptions(
            array(
                'label' => 'Files',
                'count' => $count,
                'should_create_template' => $should_create_template,
                'allow_add' => $allow_add,
            ));
        $this->setTargetElement($target_element);
        $this->setAttributes(
            array(
                'class' => 'collection-container',
                'id' => $id
            ));
    }
}
