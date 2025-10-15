<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Ads\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Theme\API\Common;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;
use System\Captcha\CaptchaFactory;
use Zend\Filter;
use Zend\InputFilter\FileInput;


class NewAdsRef extends BaseForm
{
    private $roleArray;
    private $userArray;

    public function __construct($roleArray, $userArray)
    {
        $this->roleArray = $roleArray;
        $this->userArray = $userArray;
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', url('admin/ad'));
        parent::__construct('new_ads_ref_form');
    }

    protected function addElements()
    {
        /*$this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'adId',
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'senderId',
        ));*/

        $this->add((array(
            'name' => 'roleId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_ROLES',
                'value_options' => $this->roleArray,
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'size' => 10,
                'class' => 'select2',
            )
        )));

        $this->add((array(
            'name' => 'userId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_USERS',
                'value_options' => $this->userArray,
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'size' => 10,
                'class' => 'select2',
            )
        )));

        $this->add(new Buttons('new_ads_ref_form'));

    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        $this->setRequiredFalse($filter, array(
            'roleId',
            'userId',
        ));

    }
}
