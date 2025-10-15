<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 10:21 AM
 */

namespace User\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\I18n\Filter\Alnum;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Zend\Filter;

class Edit extends BaseForm
{
    /**
     * @var Adapter
     */
    private $_adapter;

    private $_roles = false;
    private $username;
    private $email;
    private $countryId;
    private $stateId;
    private $cityId;

    public function __construct(Adapter $adapter, $username, $email, $countryId, $stateId, $cityId, $roles = false)
    {
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->_adapter = $adapter;
        $this->_roles = $roles;
        $this->username = $username;
        $this->email = $email;
        $this->countryId = $countryId;
        $this->stateId = $stateId;
        $this->cityId = $cityId;
        parent::__construct('user');
    }

    protected function addElements()
    {
        $account = new Fieldset('account');
        if ($this->_roles) {
            $account->add(array(
                'name' => 'roles',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Roles',
                    'value_options' => $this->_roles
                ),
                'attributes' => array(
                    'multiple' => 'multiple',
                    'size' => 5
                )
            ));
        }
        $account->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
            'options' => array(),
        ));
        $account->add(array(
            'name' => 'username',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Username',
                'description' => 'Username should be between 5 to 25 characters. The characters are limited to English Alphabets and numbers'
            ),
        ));

        $account->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(),

        ));

        $account->add(array(
            'name' => 'displayName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Display Name',
                'description' => 'A User friendly name to display in place of Username'
            ),
            'attributes' => array(),

        ));

        $this->add($account);
        $this->add(new Profile($this->countryId, $this->stateId, $this->cityId));

        $this->add(new Buttons('edit_profile'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $password \Zend\InputFilter\Input
         * @var $password_verify \Zend\InputFilter\Input
         * @var $email \Zend\InputFilter\Input
         * @var $username \Zend\InputFilter\Input
         * @var $displayName \Zend\InputFilter\Input
         * @var $roles \Zend\InputFilter\Input
         */
        $filter = $this->getInputFilter();


        $account = $filter->get('basic');
        $profile = $filter->get('profile');

        if ($this->_roles) {
            $roles = $account->get('roles');
        }
        $username = $account->get('username');
        $username
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)))
            ->attach(new \Zend\Validator\Db\NoRecordExists(
                array(
                    'table' => 'tbl_users',
                    'field' => 'username',
                    'adapter' => $this->_adapter,
                    'exclude' => array(
                        'field' => 'username',
                        'value' => $this->username
                    ),
                    'messages' => array(
                        \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'This username is already taken'
                    )
                )
            ));
        $username->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Alnum(false, 'en_US'))
            ->attach(new Filter\StripTags());
        //TODO add a filter to allow _

        $email = $account->get('email');
        $email
            ->setAllowEmpty(true)
            ->setRequired(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress())
            ->attach(new NoRecordExists(
                array(
                    'table' => 'tbl_users',
                    'field' => 'email',
                    'adapter' => $this->_adapter,
                    'exclude' => array(
                        'field' => 'email',
                        'value' => $this->email
                    ),
                    'messages' => array(
                        \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'This Email address belongs to another user'
                    )
                )));
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());


        $displayName = $account->get('displayName');
        $displayName
            ->setAllowEmpty(true)
            ->setRequired(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)));
        $displayName->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());

        $countryId = $profile->get('countryId');
        $countryId->setRequired(false);

        $stateId = $profile->get('stateId');
        $stateId->setRequired(false);

        $cityId = $profile->get('cityId');
        $cityId->setRequired(false);

        $profile->get('mobile')->setRequired(true);
        $profile->get('mobile')->allowEmpty(false);
    }
}
