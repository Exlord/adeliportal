<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/2/13
 * Time: 9:54 AM
 */

namespace User\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use System\Validator\Username;
use User\Form\Config\FieldsAccess;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Fieldset;
use Zend\I18n\Filter\Alnum;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\StringLength;
use Zend\Filter;
use User\Module as UserModule;

class User extends BaseForm
{
    private $_adapter;
    private $profile;
    private $countryId;
    private $stateId;
    private $cityId;
    private $username = null;
    private $email = null;
    private $password;
    private $userFields;

    public function __construct(Adapter $adapter, $password = true, $username = null, $email = null, $profile = false, $countryId = array(), $stateId = array(), $cityId = array(), $userFields = null)
    {
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->_adapter = $adapter;
        $this->profile = $profile;
        $this->countryId = $countryId;
        $this->stateId = $stateId;
        $this->cityId = $cityId;
        $this->username = $username;
        $this->profile = $profile;
        $this->password = $password;
        $this->email = $email;
        $this->userFields = $userFields;
        parent::__construct('new_user');
    }

    protected function addElements()
    {
        $this->add(new Basic($this->password, $this->userFields));

        if (isAllowed(UserModule::ADMIN_USER_EDIT_ROLE)) {
            /* @var $roleTable \User\Model\RoleTable */
            $roleTable = getSM('role_table');
            $roles = $roleTable->makeIndentedArray($roleTable->getVisibleRoles(), 'roleName');
            unset($roles[1]);
            $this->add(array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'roles',
                'options' => array(
                    'label' => 'User Roles',
                    'value_options' => $roles,
                ),
                'attributes' => array(
                    'class' => 'select2',
                    'multiple' => 'multiple'
                )
            ));
        }
        if ($this->profile)
            $this->add(new Profile($this->countryId, $this->stateId, $this->cityId));

        $this->add(new Buttons('new_user'));
    }

    public function addInputFilters($filters = array())
    {
        parent::addInputFilters($filters);

        /**
         * @var $password \Zend\InputFilter\Input
         * @var $password_verify \Zend\InputFilter\Input
         * @var $email \Zend\InputFilter\Input
         * @var $username \Zend\InputFilter\Input
         * @var $displayName \Zend\InputFilter\Input
         * @var $roles \Zend\InputFilter\Input
         */
        $filter = $this->getInputFilter();
        $basic = $filter->get('basic');

        $NoRecordExists = array(
            'table' => 'tbl_users',
            'field' => 'username',
            'adapter' => $this->_adapter,
            'messages' => array(
                \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'This username is already taken'
            )
        );
        if ($this->username) {
            $NoRecordExists['exclude'] = array(
                'field' => 'username',
                'value' => $this->username
            );
        }

        $username = $basic->get('username');
        $username
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 25)))
            ->attach(new \Zend\Validator\Db\NoRecordExists($NoRecordExists))
            ->attach(new Username());
        $username->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());
        //TODO add a filter to allow _

        if ($this->password) {
            if ($basic->has('password')) {
                $password = $basic->get('password');
                $password
                    ->setAllowEmpty(false)
                    ->setRequired(true)
                    ->getValidatorChain()->attach(new StringLength(array('min' => 5, 'max' => 25)));
                $password->getFilterChain()
                    ->attach(new Filter\StringTrim())
                    ->attach(new Filter\StripTags());
            }

            if ($basic->has('password_verify')) {
                $password_verify = $basic->get('password_verify');
                $password_verify
                    ->setAllowEmpty(false)
                    ->setRequired(true)
                    ->getValidatorChain()
                    ->attach(new StringLength(array('min' => 5, 'max' => 25)))
                    ->attach(new Identical(array('token' => 'password',
                        'messages' => array(Identical::NOT_SAME => 'Password and Verify Password do not match'))));
                $password_verify->getFilterChain()
                    ->attach(new Filter\StringTrim())
                    ->attach(new Filter\StripTags());
            }
        }

        $NoRecordExists = array(
            'table' => 'tbl_users',
            'field' => 'email',
            'adapter' => $this->_adapter,

            'messages' => array(
                \Zend\Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => 'This Email address belongs to another user'
            )
        );
        if ($this->email) {
            $NoRecordExists['exclude'] = array(
                'field' => 'email',
                'value' => $this->email
            );
        }

        $email = $basic->get('email');
        $email
            ->setAllowEmpty(true)
            ->setRequired(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress())
            ->attach(new NoRecordExists($NoRecordExists));
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());


        $displayName = $basic->get('displayName');
        $displayName
            ->setAllowEmpty(true)
            ->setRequired(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)));
        $displayName->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());

        if ($this->profile) {
            $profile = $filter->get('profile');
            $profile->get('countryId')->setRequired(false);
            $profile->get('stateId')->setRequired(false);
            $profile->get('cityId')->setRequired(false);
        }
    }
}