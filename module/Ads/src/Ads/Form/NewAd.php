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
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\EmailAddress;
use Zend\Validator\File\FilesSize;
use Zend\Validator\File\ImageSize;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\Size;
use Zend\Validator\StringLength;
use System\Captcha\CaptchaFactory;
use Zend\Filter;
use Zend\InputFilter\FileInput;


class NewAd extends BaseForm implements InputFilterProviderInterface
{
    private $adConfig;
    private $catArray;
    private $starCountArray;
    private $state_list;
    private $city_list;
    private $baseTypeRoute;
    private $isRequestRoute;
    private $route_prefix;
    private $sendRequestTypeArray;

    public function __construct($adConfig, $catArray, $starCountArray, $state_list, $city_list, $baseTypeRoute, $isRequestRoute, $route_prefix, $sendRequestTypeArray)
    {
        $this->adConfig = $adConfig;
        $this->catArray = $catArray;
        $this->starCountArray = $starCountArray;
        $this->state_list = $state_list;
        $this->city_list = $city_list;
        $this->baseTypeRoute = $baseTypeRoute;
        $this->isRequestRoute = $isRequestRoute;
        $this->route_prefix = $route_prefix;
        $this->sendRequestTypeArray = $sendRequestTypeArray;
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', url('admin/ad'));
        parent::__construct('new_ad_form');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $adArray = null;
        foreach ($this->adConfig as $key => $row) {
            if ($row['regType'] == $this->isRequestRoute) {
                $image = '';
                $title = '';
                $className = '';
                if (isset($row['image']) && $row['image'])
                    $image = Common::Img($row['image']) /* . $row['image'] . '" class="ads_pack_icon ads_icon_.' . $row['baseType_name'] . $row['secondType_name'] . $row['timeAds'] . '" >'*/
                    ;
                if (isset($row['showTitlePack']) && !$row['showTitlePack'])
                    $className = 'ads_hidden';
                $title .= '<span class="ads_pack_title ' . $className . ' a_t_b_' . $row['baseType'] . '">' . $row['baseType_name'] . ' , </span>';
                $title .= '<span class="ads_pack_title ' . $className . ' a_t_s_' . $row['secondType'] . '">' . $row['secondType_name'] . ' </span>';
                $title .= '<span class="ads_pack_title ' . $className . ' a_t_m_' . $row['timeAds'] . '">' . $row['timeAds'] . t('ADS_MONTHLY') . ' </span>';
                $title .= '<span class="ads_pack_title">' . t('ADS_STAR_PRICE') . ' : </span>';
                $title .= '<span class="ads_pack_title ' . $className . ' a_t_star_price" >' . $row['starPrice_name'] . '</span>';
                $adArray[$key] = $image . $title;
            }
        }
        if (!$adArray)
            $adArray = array('' => 'ADS_NOT_FOUND');
        $this->add(array(
            'name' => 'adType',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'ADS_TYPE',
                'value_options' => $adArray,
                'label_options' => array(
                    'disable_html_escape' => true,
                )
            ),
            'attributes' => array(
                'class' => 'as_type',
            )
        ));

        $this->add((array(
            'name' => 'catId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                //'label' => 'Categories',
                'value_options' => $this->catArray,
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'size' => 10,
                'class' => 'select2',
            )
        )));

        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'options' => array( // 'label' => 'ADS_TITLE',
            ),
        ));

        $this->add(array(
            'name' => 'text',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Description',
            ),
        ));

        $this->add(array(
            'name' => 'googleLatLong',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add((array(
            'name' => 'starCount',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                // 'label' => 'ADS_STAR_COUNT',
                'value_options' => $this->starCountArray,
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        )));

        $this->add(array(
            'name' => 'link',
            'type' => 'Zend\Form\Element\Text',
            'options' => array( // 'label' => 'Link',
            ),
            'attributes' => array(
                'class' => 'ad_view_input_link',
            )
        ));

        $this->add((array(
            'name' => 'stateId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                // 'label' => 'State',
                'empty_option' => '-- Select --',
                'value_options' => $this->state_list,
            ),
            'attributes' => array(
                'data-cityid' => 'cityId',
                'id' => 'stateId',
                'class' => 'state-selector select2',
            )
        )));

        $this->add((array(
            'name' => 'cityId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                // 'label' => 'City',
                'empty_option' => '-- Select --',
                'value_options' => $this->city_list,
            ),
            'attributes' => array(
                'id' => 'cityId',
                'class' => 'city-selector select2',
            )
        )));

        $this->add(array(
            'name' => 'address',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array( // 'label' => 'Address',
            ),
        ));

        $this->add(array(
            'name' => 'fax',
            'type' => 'Zend\Form\Element\Text',
            'options' => array( //'label' => 'Fax',
            ),
        ));

        $this->add(array(
            'name' => 'mobile',
            'type' => 'Zend\Form\Element\Text',
            'options' => array( // 'label' => 'Mobile',
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Text',
            'options' => array( // 'label' => 'Name',
            ),
        ));

        $this->add(array(
            'name' => 'mail',
            'type' => 'Zend\Form\Element\Text',
            'options' => array( //  'label' => 'Email',
            ),
        ));

        $this->add(array(
            'name' => 'showPm',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'ADS_SHOW_PM'
            ),
        ));

        $this->add(array(
            'name' => 'keyword',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                // 'label' => 'ADS_KEYWORDS',
                'description' => t('ADS_KEYWORD_DESC0') . t('ADS_KEYWORD_DESC'),
            ),
        ));

        $this->add(array(
            'name' => 'smallImage',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(),
            'options' => array(
                'label' => 'ADS_SMALL_IMAGE',
            ),
        ));

        if (is_array($this->sendRequestTypeArray)) {

            $this->add(array(
                'name' => 'notifyType',
                'type' => 'Zend\Form\Element\Hidden',
                'options' => array( // 'label' => '',
                ),
            ));

            $this->add(array(
                'name' => 'sendRequestType',
                'type' => 'Zend\Form\Element\Radio',
                'options' => array(
                    // 'label' => 'ADS_TYPE',
                    'value_options' => $this->sendRequestTypeArray,
                ),
                'attributes' => array(
                    'class' => 'send_request_type',
                )
            ));
        }

        $images = new \Ads\Form\NewAdImageCollection();
        $this->add($images);

        $fields = new \System\Form\Fieldset('fields');
        $this->add($fields);
        $inputFilters = getSM()->get('fields_api')->loadFieldsByType('ads_' . $this->baseTypeRoute . '_' . $this->isRequestRoute, $this, $fields);



        if ($this->route_prefix == 'app') {
            $this->add(CaptchaFactory::create());
        }

        $this->add(new Buttons('new_ad_form'));

        if (count($inputFilters))
            $fields->setInputFiltersConfig($inputFilters);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $filter = $this->inputFilters;
        $filter['name'] = array(
            'name' => 'name',
            'require' => true,
            'allow_empty' => false,
        );
        $filter['mail'] = array(
            'name' => 'mail',
            'require' => true,
            'allow_empty' => false,
        );
        $filter['mobile'] = array(
            'name' => 'mobile',
            'require' => true,
            'allow_empty' => false,
        );
        $filter['catId'] = array(
            'name' => 'catId',
            'require' => false,
            'allow_empty' => true,
        );
        $filter['keyword'] = array(
            'name' => 'keyword',
            'require' => false,
            'allow_empty' => true,
        );

        $filter['cityId'] = array(
            'name' => 'cityId',
            'require' => true,
            'allow_empty' => false,
        );

        $filter['stateId'] = array(
            'name' => 'stateId',
            'require' => true,
            'allow_empty' => false,
        );

        $filter['smallImage'] = array(
            'name' => 'smallImage',
            'require' => false,
            'allow_empty' => true,
            'validators' => array(
                new Size(array('max' => 512000)),
                new MimeType('image'),
                new ImageSize(array('maxWidth' => 2000, 'maxHeight' => 2000)),
            ),
        );

        $filter['mail'] = array(
            'name' => 'mail',
            'require' => false,
            'allow_empty' => true,
            'filters' => array(
                new Filter\StringTrim(),
                new Filter\StripTags()
            ),
            'validators' => array(
                new StringLength(array('min' => 5, 'max' => 200)),
                new EmailAddress(),
            ),
        );

        if ($this->has('sendRequestType'))
            $filter['sendRequestType'] = array(
                'name' => 'sendRequestType',
                'require' => false,
                'allow_empty' => true,
            );
        return $filter;
    }
}
