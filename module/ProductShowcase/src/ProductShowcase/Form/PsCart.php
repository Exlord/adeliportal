<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace ProductShowcase\Form;

use Application\API\App;
use System\Captcha\CaptchaFactory;
use Zend\Captcha;
use Zend\Filter;
use Zend\Filter\File\RenameUpload;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\I18n\Filter\Alnum;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use System\Form\BaseForm;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

class PsCart extends BaseForm
{
    private $country_list = array();
    private $state_list = array();
    private $city_list = array();

    public function __construct($country_list,$state_list,$city_list)
    {
        $this->country_list = $country_list;
        $this->state_list = $state_list;
        $this->city_list = $city_list;
        parent::__construct('ps_cart_form');
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'family',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Family',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'company',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Company',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'mail',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Phone',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'fax',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Fax',
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'countryId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Country',
                'description' => '',
                'empty_option' => '-- Select --',
                'value_options' => $this->country_list,
            ),
            'attributes' => array(
                'data-stateid' => 'stateId',
                'class' => 'country-selector select2',
                'style' => 'max-width:250px;'
            ),

        ));

        $this->add(array(
            'name' => 'stateId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'State',
                'empty_option' => '-- Select --',
                'value_options' => $this->state_list
            ),
            'attributes' => array(
                'data-cityid' => 'cityId',
                'class' => 'state-selector select2',
                'style' => 'max-width:250px;'
            ),

        ));
        $this->add(array(
            'name' => 'cityId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'City',
                'empty_option' => '-- Select --',
                'value_options' => $this->city_list
            ),
            'attributes' => array(
                //'data-areaId' => 'area',
                'class' => 'city-selector select2',
                'style' => 'max-width:250px;'
            ),
        ));


        $this->add(array(
            'name' => 'address',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Address'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'desc',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Description'
            ),
            'attributes' => array()
        ));

        $this->add(CaptchaFactory::create());

        $this->add(new \System\Form\Buttons($this->getName()));
    }

    public function addInputFilters($filters = array())
    {
        parent::addInputFilters($filters);

        $filter = $this->getInputFilter();


        #filter by digit only
        $this->filterByDigit($filter, array(//'totalPrice',
        ));

        $this->filterByTrimAndTags($filter, array(
            'address',
            'phone',
            'fax',
        ));

        $this->setRequiredFalse($filter, array(
            'countryId',
            'stateId',
            'cityId',
        ));


        $addressFull = $filter->get('address');
        $addressFull->setRequired(true);

        $ownerName = $filter->get('name');
        $ownerName->setRequired(true);
        $ownerName->getFilterChain()
            ->attach(new \Zend\Filter\StringTrim())
            ->attach(new \Zend\Filter\StripTags());
        $ownerName->getValidatorChain()
            ->attach(new \Zend\Validator\StringLength(array('max' => 200)))
            // ->attach(new \Zend\I18n\Validator\Alpha)
            ->attach(new \Zend\Validator\NotEmpty());

        $email = $filter->get('mail');
        $email
            ->setAllowEmpty(false)
            ->setRequired(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress());
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());
    }

}
