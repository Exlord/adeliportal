<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Application\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

class GlobalConfig extends BaseForm
{

    public function __construct()
    {
        $this->setAttribute('class', 'normal-form');
        parent::__construct('global_config');
        $this->setAttribute('action',url('admin/configs/global'));
    }

    protected function addElements()
    {
        $realEstate = new \Application\Form\GlobalConfigRealEstate();
        $this->add($realEstate);

        $this->add(new \System\Form\Buttons('global_config'));
    }

    protected function addInputFilters()
    {
        // TODO: Implement addInputFilters() method.
    }
}
