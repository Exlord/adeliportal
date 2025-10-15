<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OnlineOrders\Form;

use Zend\Captcha;
use Zend\Filter\File\RenameUpload;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use System\Form\BaseForm;

class OrderForms extends BaseForm
{



    public function __construct()
    {
        parent::__construct('order_form');
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'partWebsite',
            'type' => 'Zend\Form\Fieldset',
            'options' => array(
                'label' => 'Disabled Items',
                'description' => 'Disabled Items for Estate Types. The Selected Items will be disabled'
            )
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-show',
            'attributes' => array(
                'value' => 'Show',
                'class' => 'button',
            ),

        ));

    }

    public function addInputFilters()
    {


    }

}
