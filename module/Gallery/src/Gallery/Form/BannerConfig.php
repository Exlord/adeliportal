<?php
namespace Gallery\Form;

use System\Form\BaseForm;
use Zend\Form\Fieldset;

class BannerConfig extends BaseForm
{
    private $position = array();
    private $mailTemplate = array();

    public function __construct($position,$mailTemplate)
    {
        $this->position = $position;
        $this->mailTemplate = $mailTemplate;
        parent::__construct('banner_config');
        $this->setAttributes(array(
            'action' => url('admin/banner/configs'),
            'class' => 'normal-form ajax_submit',
        ));
    }

    public function addElements()
    {

        $count = new Fieldset('countPosition');
        $count->setLabel('The number of banners in every position');
        //  $filed->setOptions(array('description' => ''));
        $this->add($count);
        foreach ($this->position as $key => $val) {
            $count->add(array(
                'name' => $key,
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'spinner withcomma'
                ),
                'options' => array(
                    'description' => '',
                    'label' => $val
                ),
            ));
        }

        $this->add(array(
            'name' => 'orderBannerValidate',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Form of payment confirmation email',
                'empty_option' => '-- Select --',
                'value_options' => $this->mailTemplate,
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));
        $this->add(array(
            'name' => 'orderBannerExpired',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Form of SMS to inform the order expires',
                'empty_option' => '-- Select --',
                'value_options' => $this->mailTemplate,
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(new \System\Form\Buttons('banner_config'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        #filter by digit only
        $nameInput = array();
        foreach ($this->position as $key => $val) {
            $nameInput[] = $key;
        }
        $this->filterByDigit($filter->get('countPosition'), $nameInput);

        $this->setRequiredFalse($filter,array(
            'orderBannerValidate',
            'orderBannerExpired',
        ));

    }


}
