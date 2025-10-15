<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Ads\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\StringLength;
use Zend\Filter;

class AdvanceConfig extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('advance_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('action', url('admin/ad/config/advance-config'));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'showKeyword',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_SHOW_KEYWORD',
                //'description'=>'ADS_STAR_COUNT_DESC',
                'value_options' => array(
                    '0'=>'ADS_NOT_DISPLAY',
                    '1'=>'ADS_DISPLAY',
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'showCategory',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_SHOW_CATEGORY',
               // 'description'=>'ADS_STAR_COUNT_DESC',
                'value_options' => array(
                    '0'=>'ADS_NOT_DISPLAY',
                    '1'=>'ADS_DISPLAY',
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'showHits',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_SHOW_HITS',
               // 'description'=>'ADS_STAR_COUNT_DESC',
                'value_options' => array(
                    '0'=>'ADS_NOT_DISPLAY',
                    '1'=>'ADS_DISPLAY',
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'showCreateDate',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_SHOW_CREATE_DATE',
               // 'description'=>'ADS_STAR_COUNT_DESC',
                'value_options' => array(
                    '0'=>'ADS_NOT_DISPLAY',
                    '1'=>'ADS_DISPLAY',
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'showStatusType',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'ADS_SHOW_STATUS_TYPE',
                //'description'=>'ADS_STAR_COUNT_DESC',
                'value_options' => array(
                    '0'=>'ADS_DONT_DISPLAY_REVOKED',
                    '1'=>'ADS_DISPLAY_REVOKED',
                    '2'=>'ADS_DISPLAY_REVOKED_BUT_DONT_PM',
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'cookieTime',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_COOKIE_TIME',
                 'description'=>'ADS_COOKIE_TIME_DESC',
                'value_options' => array(
                    '2'=>'ADS_2_DAY',
                    '5'=>'ADS_5_DAY',
                    '8'=>'ADS_8_DAY',
                    '10'=>'ADS_10_DAY',
                    '15'=>'ADS_15_DAY',
                    '20'=>'ADS_20_DAY',
                    '30'=>'ADS_30_DAY',
                    '0'=>'ADS_NEVER',
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(new \System\Form\Buttons('advance_config'));
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
