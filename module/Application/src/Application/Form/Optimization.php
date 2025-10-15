<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/7/13
 * Time: 8:03 PM
 */

namespace Application\Form;


use System\Form\BaseForm;
use System\Form\Buttons;

class Optimization extends BaseForm
{
    public function __construct()
    {
        $url = url('admin/optimization');
        //don't submit this form with ajax
        $this->setAttribute('class', 'force-normal');
        $this->setAction($url);
        parent::__construct('optimization');
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'combine_css',
            'options' => array(
                'label' => 'Combine & Minify CSS',
                'help-block' => 'combine and minify css files to improve performance',
//                'twb-layout' => 'inline',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'combine_js',
            'options' => array(
                'label' => 'Combine JS',
                'help-block' => 'combine js files to improve performance',
//                'twb-layout' => 'inline',
            ),
        ));

        $this->add(new Buttons('optimization'));
    }

    protected function addInputFilters()
    {
    }
}