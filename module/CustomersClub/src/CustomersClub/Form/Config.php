<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace CustomersClub\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
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

class Config extends BaseForm implements InputFilterProviderInterface
{
    private $baseConfig;

    public function __construct($baseConfig)
    {
        $this->baseConfig = $baseConfig;
        parent::__construct('cc_config');
        $this->setAttribute('class', 'ajax_submit');
        $this->setAction(url('admin/customers-club/config'));
        $this->setAttribute('data-cancel', url('admin/customers-club/config'));
    }

    protected function addElements()
    {
        $modules = new Fieldset('modules');
        $this->add($modules);

        foreach ($this->baseConfig as $module => $configs) {
            $moduleFieldset = new Fieldset($module);
            $moduleFieldset->setLabel($configs['label']);
            $modules->add($moduleFieldset);
            foreach ($configs['events'] as $name => $options) {
                $moduleFieldset->add(array(
                    'name' => $name,
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => $options['label'],
                        'description' => $options['description']
                    ),
                    'attributes' => array(
                        'class' => 'spinner',
                        'data-step' => 1
                    )
                ));
            }

            getSM('cc_api')->loadConfigs($moduleFieldset);
        }

        $this->add(new Buttons('cc_config'));
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
