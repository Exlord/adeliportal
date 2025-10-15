<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace RealEstate\Form;

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

class RealEstate extends BaseForm
{
    private $expireTime;
    private $regType;
    private $estateType;
    private $stateId;
    private $cityId;
    private $areaId;
    private $numberOfImages = 1;
    private $adminRoute;
    private $app;

    protected $loadInputFilters = false;

    public function __construct($expireTime, $regType, $estateType, $stateId, $cityId, $numberOfImages = 1, $isRequest = false, $adminRoute = 'app', $areaId = array(), $app = 0)
    {
        $this->expireTime = $expireTime;
        $this->regType = $regType;
        $this->estateType = $estateType;
        $this->stateId = $stateId;
        $this->cityId = $cityId;
        $this->areaId = $areaId;
        $this->numberOfImages = $numberOfImages;
        $this->isRequest = $isRequest;
        $this->adminRoute = $adminRoute;
        $this->app = $app;
        parent::__construct('real_state_form');
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'ownerMobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Mobile',
                //'description' => 'Mobile Number Necessary to receive sms'
            ),
            'attributes' => array(
                //'placeholder' => t('Mobile Number Necessary to receive sms'),
                'class' => 'placeholder-text num'
            )
        ));

        $this->add(array(
            'name' => 'stateId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'State',
                'value_options' => $this->stateId
            ),
            'attributes' => array(
                'data-cityid' => 'cityId',
                'class' => 'state-selector select2'
            ),

        ));
        $this->add(array(
            'name' => 'cityId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'City',
                'value_options' => $this->cityId
            ),
            'attributes' => array(
                'data-areaId' => 'areaId',
                'class' => 'city-selector select2'
            ),
        ));

        $this->add(array(
            'name' => 'areaId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Region',
                'empty_option' => '-- Select --',
                'value_options' => $this->areaId
            ),
            'attributes' => array(
                'class' => 'area-selector select2'
            ),
        ));

        $this->add(array(
            'name' => 'newArea',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name',
            ),
            'attributes' => array(
                'placeholder' => t('New Area'),
                //'class' => 'hidden',
            )
        ));

        $this->add(array(
            'name' => 'ownerName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Name',
            ),
            'attributes' => array(
                'placeholder' => t('Name'),
            )
        ));

        $this->add(array(
            'name' => 'ownerPhone',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Phone'
            ),
            'attributes' => array(
                'placeholder' => t('Phone'),
                'class' => 'num',
            )
        ));

        $this->add(array(
            'name' => 'ownerEmail',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Email'
            ),
            'attributes' => array(
                'placeholder' => t('Email'),
            )
        ));

        $this->add(array(
            'name' => 'regType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Register Type',
                'value_options' => $this->regType
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));
        $this->add(array(
            'name' => 'estateType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Estate Type',
                'value_options' => $this->estateType
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));


        $this->add(array(
            'name' => 'expire',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Expire Time',
                'value_options' => $this->expireTime,
                //'description' => 'Expire Time'
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'addressShort',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Address Short'
            ),
            'attributes' => array(
                'placeholder' => t('Address Short'),
            )
        ));
        $this->add(array(
            'name' => 'addressFull',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Address'
            ),
            'attributes' => array(
                'placeholder' => t('Address'),
            )
        ));
        $this->add(array(
            'name' => 'mortgagePrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => t('RealEstate_mortgage_price') . ' ' . t(getCurrency())
            ),
            'attributes' => array(
                'class' => 'num withcomma spinner',
                // 'placeholder' => t('Mortgage Price'),
            )
        ));
        $this->add(array(
            'name' => 'totalPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => t('RealEstate_total_price') . ' ' . t(getCurrency())
            ),
            'attributes' => array(
                'class' => 'num withcomma spinner',
                //'placeholder' => t('Total Price'),
            )
        ));
        $this->add(array(
            'name' => 'priceOneMeter',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => t('RealEstate_price_one_meter') . ' ' . t(getCurrency())
            ),
            'attributes' => array(
                'class' => 'num withcomma spinner',
                //  'placeholder' => t('Price one meter'),
            )
        ));
        $this->add(array(
            'name' => 'rentalPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => t('RealEstate_rental_price') . ' ' . t(getCurrency())
            ),
            'attributes' => array(
                'class' => 'num withcomma spinner',
                //'placeholder' => t('Rental Price'),
            )
        ));

        $this->add(array(
            'name' => 'googleLatLong',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Extra Description'
            ),
            'attributes' => array(
                'placeholder' => t('Extra Description'),
            )
        ));

        $this->add(array(
            'name' => 'pm',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Private Message'
            ),
            'attributes' => array(
                'placeholder' => t('Private Message'),
            )
        ));

        if (!$this->isRequest) {
            $this->add(array(
                'name' => 'isSpecial',
                'type' => 'Zend\Form\Element\Checkbox',
                'options' => array(
                    'label' => 'Special Realty',
                    'description' => 'Ads which is submitted as special will be shown before other ads and as bold. <br/> Special ads will cost %s Rial.'
                ),
            ));
            $this->add(array(
                'name' => 'showInfo',
                'type' => 'Zend\Form\Element\Checkbox',
                'options' => array(
                    'label' => 'Name and contact number is visible ?',
                    'description' => 'Ads that ask the user immediately and communicate directly with the applicant. User the name and contact number at the bottom of the home file will be displayed. <br/> This ads is a monthly fee of $ 1,000 riyals.'
                ),
            ));
        }

        if ($this->numberOfImages) {
            $this->add(array(
                'type' => 'Zend\Form\Element\Collection',
                'name' => 'images',
                'options' => array(
                    'label' => 'Images',
                    'count' => $this->numberOfImages,
                    'should_create_template' => false,
                    'allow_add' => TRUE,
                    'target_element' => array(
                        'type' => 'Zend\Form\Element\File'
                    )
                ),
                'attributes' => array(
                    'class' => 'collection-container',
                )
            ));
        }

        if ($this->adminRoute == 'app') {
            $this->add(CaptchaFactory::create());
        }
        $this->add(new \System\Form\Buttons($this->getName()));
    }

    public function addInputFilters($filters = array())
    {
        parent::addInputFilters($filters);

        $filter = $this->getInputFilter();


        #filter by digit only
        $this->filterByDigit($filter, array(
            'totalPrice',
            'priceOneMeter',
            'mortgagePrice',
            'rentalPrice',
            'ownerMobile',
        ));

        $this->filterByTrimAndTags($filter, array(
            'addressShort',
            'addressFull',
            'ownerPhone',
        ));

        $this->setRequiredFalse($filter, array(
            'areaId',
        ));

        if ($filter->has('isSpecial'))
            $this->setRequiredFalse($filter, array(
                'isSpecial',
            ));

        if ($filter->has('showInfo'))
            $this->setRequiredFalse($filter, array(
                'showInfo',
            ));

        if (!$this->app) {
            $ownerPhone = $filter->get('ownerPhone');
            $ownerPhone->setRequired(true);
        }

        $addressFull = $filter->get('addressFull');
        $addressFull->setRequired(true);

        $ownerName = $filter->get('ownerName');
        $ownerName->setRequired(true);
        $ownerName->getFilterChain()
            ->attach(new \Zend\Filter\StringTrim())
            ->attach(new \Zend\Filter\StripTags());
        $ownerName->getValidatorChain()
            ->attach(new \Zend\Validator\StringLength(array('max' => 200)))
            // ->attach(new \Zend\I18n\Validator\Alpha)
            ->attach(new \Zend\Validator\NotEmpty());

        if (!$this->app) {
            $ownerName = $filter->get('ownerMobile');
            $ownerName->setRequired(true);
            $ownerName->getValidatorChain()
                ->attach(new \Zend\Validator\StringLength(array('max' => 11)))
                ->attach(new \Zend\Validator\NotEmpty());
        }

        $email = $filter->get('ownerEmail');
        $email
            ->setAllowEmpty(false)
            ->setRequired(false)
            ->getValidatorChain()
            ->attach(new StringLength(array('min' => 5, 'max' => 200)))
            ->attach(new EmailAddress());
        $email->getFilterChain()
            ->attach(new Filter\StringTrim())
            ->attach(new Filter\StripTags());

        if ($this->numberOfImages) {
            $fileCollection = $filter->get('images');
            for ($i = 0; $i < $this->numberOfImages; $i++) {
                /* @var $fPath FileInput */
                $file = new FileInput($i);
                $file->setRequired(false)
                    ->getFilterChain()->attach(
                        new RenameUpload(
                            array(
                                'target' => PUBLIC_FILE . '/temp',
                                'randomize' => true,
                            )
                        )
                    );
                $fileCollection->add($file);
            }
        }

    }

}
