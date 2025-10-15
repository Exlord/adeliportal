<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Ads\Form\NewTypeConfig;

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

class Config extends BaseForm implements InputFilterProviderInterface
{
    public function __construct()
    {
        parent::__construct('new_type_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('action', url('admin/ad/config/new-type'));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'starCount',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_STAR_COUNT',
                'description'=>'ADS_STAR_COUNT_DESC',
                'value_options' => array(
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10,
                )
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name'=>'keywordCount',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'ADS_KEYWORD_COUNT',
            )
        ));

        $this->add(array(
            'name'=>'smallImgWidth',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'ADS_SMALL_IMG_WIDTH',
                'description'=>'ADS_SMALL_IMG_WIDTH_DESC'
            )
        ));

        $this->add(array(
            'name'=>'smallImgHeight',
            'type'=>'Zend\Form\Element\Text',
            'options'=>array(
                'label'=>'ADS_SMALL_IMG_HEIGHT',
                'description'=>'ADS_SMALL_IMG_HEIGHT_DESC'
            )
        ));

        $this->add(new BaseTypeAdsCollection());

        $this->add(new \System\Form\Buttons('new_type_config'));
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
