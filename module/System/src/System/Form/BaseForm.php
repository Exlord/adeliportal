<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/13/13
 * Time: 12:12 PM
 */

namespace System\Form;


use Zend\Cache\Exception\MissingDependencyException;
use Zend\Di\Exception\MissingPropertyException;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Csrf;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods;

abstract class BaseForm extends Form
{
    protected $loadInputFilters = true;
    protected $inputFilters = array();

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'post')
            ->setHydrator(new ClassMethods(false));

        $this->addElements();
        if ($this->loadInputFilters)
            $this->addInputFilters();
    }

    abstract protected function addElements();

    public function setInputFiltersConfig(array $inputFilters)
    {
        $this->inputFilters = $inputFilters;
    }

    protected function addInputFilters(array $filters = array(), $parent = null)
    {
        if (count($filters)) {
            $filter = $this->getInputFilter();
            if ($parent)
                $filter = $parent;
            foreach ($filters as $name => $params) {
                if (isset($params['name'])) {
                    /* @var $input Input */
                    $input = $filter->get($params['name']);
                    if (isset($params['required']))
                        $input->setRequired($params['required']);
                    if (isset($params['allow_empty']))
                        $input->setAllowEmpty($params['allow_empty']);
                    if (isset($params['filters'])) {
                        foreach ($params['filters'] as $f) {
                            if (isset($f['name'])) {
                                $options = isset($f['options']) ? $f['options'] : array();
                                $input->getFilterChain()->attachByName($f['name'], $options);
                            }
                        }
                    }
                    if (isset($params['validators'])) {
                        foreach ($params['validators'] as $v) {
                            if (isset($v['name'])) {
                                $options = isset($v['options']) ? $v['options'] : array();
                                $input->getValidatorChain()->attachByName($v['name'], $options);
                            }
                        }
                    }
                } elseif (is_array($params))
                    self::addInputFilters($params, $filter->get($name));
            }
        }
    }

    /**
     * @param $action
     * @return BaseForm
     */
    public function setAction($action)
    {
        $this->setAttribute('action', $action);
        return $this;
    }

    public function prepare()
    {
        if ($this->getAttribute('action') == null)
            throw new MissingPropertyException('Forms Action Attribute is missing!');
        $parent = parent::prepare();

        /* @var $inputFilter InputFilter */
        $inputFilter = $this->getInputFilter();
        $this->setRequiredIdentifier($inputFilter, $this);

        return $parent;
    }

    /**
     * @param $inputFilter InputFilter|Input
     * @param $formOrFieldset
     */
    private function setRequiredIdentifier($inputFilter, $formOrFieldset)
    {
        $inputs = $inputFilter->getInputs();
        if ($inputs && count($inputs)) {
            /* @var $input \Zend\InputFilter\Input */
            foreach ($inputs as $name => $input) {
                if ($input instanceof InputFilter) {
                    $this->setRequiredIdentifier($input, $formOrFieldset->get($name));
                } else {
                    if ($input->isRequired() || !$input->allowEmpty()) {
                        $inputName = $input->getName();
                        if ($formOrFieldset->has($inputName)) {
                            /* @var $el \Zend\Form\Element */
                            $el = $formOrFieldset->get($inputName);
                            if (!$el instanceof Checkbox && !$el instanceof Csrf) {
                                $el->setOption('required',true);
                            }
                        }
                    }
                }
            }
        }
    }

    public function filterByDigit($inputFilterObject, array $fields)
    {
        foreach ($fields as $field):

            $filter = $inputFilterObject->get($field);
            $filter->getFilterChain()
                ->attach(new \Zend\Filter\Digits())
                ->attach(new \Zend\Filter\StringTrim())
                ->attach(new \Zend\Filter\StripTags());

        endforeach;
    }

    public function setRequiredFalse($inputFilterObject, array $data)
    {
        foreach ($data as $val) {
            $required = $inputFilterObject->get($val);
            $required->setRequired(false);
        }
    }

    public function filterByTrimAndTags($inputFilterObject, array $fields)
    {
        foreach ($fields as $field) {
            $filter = $inputFilterObject->get($field);
            $filter->getFilterChain()
                ->attach(new \Zend\Filter\StringTrim())
                ->attach(new \Zend\Filter\StripTags());
        }
    }
}