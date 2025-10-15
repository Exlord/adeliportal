<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Gallery\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;


class BannerSize extends BaseForm
{
    private $_position = array();

    public function __construct($position = array())
    {
        $this->_position = $position;
        parent::__construct('banner_size');
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    public function addElements()
    {


        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'position',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Position',
                'value_options' => $this->_position,
                'description' => 'Select in witch area of the template this block should be shown'
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'width',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Width'
            ),
        ));

        $this->add(array(
            'name' => 'height',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Height'
            ),
        ));

        $this->add(array(
            'name' => 'price',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Price'
            ),
            'attributes' => array(
                'class' => 'spinner withcomma'
            ),
        ));

        $this->add(array(
            'name' => 'addPrice',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'spinner withcomma'
            ),
            'options' => array(
                'description' => t('High prices is to upload an image. If the user can upload more than one image, the number is multiplied by the price'),
                'label' => 'Per price addition image'
            ),
        ));

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Status',
                'value_options' => array(
                    '0' => 'Not Approved',
                    '1' => 'Approved'
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(new \System\Form\Buttons('banner_size'));
    }

    public function addInputFilters()
    {
        $filter = $this->getInputFilter();

        $commaArray = array(
            'addPrice',
            'price',
        );
        $this->filterByDigit($filter , $commaArray);
    }
}
