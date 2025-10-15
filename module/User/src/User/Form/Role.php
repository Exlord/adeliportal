<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace User\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class Role extends BaseForm
{
    private $roles;

    public function __construct($roles)
    {
        $this->roles = $roles;
        $this->setAttribute('class', 'normal-form ajax_submit');
        parent::__construct('roll');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'parentId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Role Parent',
                'value_options' => $this->roles
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'roleName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Role Name'
            ),
            'attributes' => array(),
        ));

        $this->add(new \System\Form\Buttons('role'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $roleName Input
         */
        $filter = $this->getInputFilter();

        $roleName = $filter->get('roleName');
        $roleName->setRequired(true)
            ->setAllowEmpty(false)
            ->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());
    }
}
