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
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;

class Upgrade extends BaseForm
{
    private $adConfig;
    private $starCountArray;

    public function __construct($adConfig, $starCountArray)
    {
        $this->adConfig = $adConfig;
        $this->starCountArray = $starCountArray;
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', url('admin/ad'));
        parent::__construct('upgrade_ad_form');
    }

    protected function addElements()
    {
        $adArray = array();
        foreach ($this->adConfig as $key => $row)
            $adArray[$key] = $row['baseType_name'] . ' * ' . $row['secondType_name'] . ' * ' . $row['timeAds'] . t('ADS_MONTHLY');
        $this->add(array(
            'name' => 'adType',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'ADS_TYPE',
                'value_options' => $adArray,
            ),
            'attributes' => array(
                'class' => 'as_type',
            )
        ));

        $this->add((array(
            'name' => 'starCount',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'ADS_STAR_COUNT',
                'value_options' => $this->starCountArray,
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        )));

        $this->add(new Buttons('upgrade_ad_form'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

    }
}
