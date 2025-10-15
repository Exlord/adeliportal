<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Ads\Form\FourConfig;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
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
    private $baseType = null;
    private $isRequest = 0;

    public function __construct($baseType, $isRequest)
    {
        $this->baseType = $baseType;
        $this->isRequest = $isRequest;
        parent::__construct('four_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('action', url('admin/ad/config/four-config'));
    }

    protected function addElements()
    {

        $this->add(new FieldsAdsCollection($this->baseType,$this->isRequest));

        $this->add(new Buttons('four_config'));
    }


    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        // TODO: Implement getInputFilterSpecification() method.
        return array();
    }
}
