<?php
namespace Gallery\Form;

use System\Form\BaseForm;

class GalleryPageConfig extends BaseForm
{
    public function __construct()
    {
        parent::__construct('gallery_page_config');
        $this->setAttributes(array(
            'action' => url('admin/gallery/configs'),
            'class' => 'normal-form ajax_submit',
        ));
    }

    public function addElements()
    {

        $this->add(array(
            'name' => 'viewType',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'View Type',
                'value_options' => array(
                    1 => 'Simple',
                    2 => 'Slide',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'slideSpeed',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Slide Speed',
                'value_options' => array(
                    1 => 'Slow',
                    2 => 'Normal',
                    3 => 'Quick',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(new \System\Form\Buttons('gallery_page_config'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();
    }


}
