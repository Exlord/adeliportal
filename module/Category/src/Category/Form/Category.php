<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Category\Form;

use System\Filter\Word\SpaceToUnderscore;
use System\Form\BaseForm;
use System\Form\Buttons;
use System\Validator\MachineName;
use Zend\Captcha;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\Filter\Word\DashToUnderscore;
use Zend\Form\Element;
use Zend\I18n\Filter\Alnum;
use Zend\I18n\Filter\Alpha;
use Zend\Validator\StringLength;

class Category extends BaseForm
{
    public function __construct()
    {
        parent::__construct('category');
        $this->setAttribute('class', 'normal-form ajax_submit');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'catName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Category Name',
                'description' => 'Category name'
            ),
        ));

        $this->add(array(
            'name' => 'catMachineName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Category Machine Name',
                'description' => 'Machine name can only contain English Alphabets and Numbers and _ and no space and no number at the beginning'
            ),
            'attributes' => array(
                'class' => 'left-align'
            )
        ));

        $this->add(array(
            'name' => 'catText',
            'type' => 'TextArea',
            'options' => array(
                'label' => 'Category Description'
            ),
        ));

        $this->add(new Buttons('category'));
    }

    protected function addInputFilters()
    {
        $filter = $this->getInputFilter();

        $catName = $filter->get('catName');
        $catName->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags());
        $catName->getValidatorChain()
            ->attach(new StringLength(array(5, 200)));

        $catName->setRequired(true);


        $catMachineName = $filter->get('catMachineName');
        $catMachineName->setRequired(true)->setAllowEmpty(false);
        $catMachineName->getFilterChain()
            ->attach(new StringTrim())
            ->attach(new StripTags())
            ->attach(new DashToUnderscore())
            ->attach(new SpaceToUnderscore());
        $catMachineName->getValidatorChain()
            ->attach(new StringLength(array(5, 200)))
            ->attach(new MachineName());
    }
}
