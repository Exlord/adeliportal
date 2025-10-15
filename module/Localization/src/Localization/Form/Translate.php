<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Localization\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Captcha;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Form\Element;


class Translate extends BaseForm
{
    private $translate_langs;
    private $localizableContent;

    public function __construct($translate_langs, $localizableContent)
    {
        $this->localizableContent = $localizableContent;
        $this->translate_langs = $translate_langs;
        parent::__construct('translate_form');
        $this->setAttributes(array(
            'class' => 'normal-form ajax_submit',
        ));
    }

    public function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Fieldset',
            'name' => 'langs-content',
        ));

        $langsContent = $this->get('langs-content');
        foreach ($this->translate_langs as $sign => $name) {
            $lang_fieldset = new Lang($sign, $this->localizableContent['fields']);
            $langsContent->add($lang_fieldset);
        }

        $this->add(new Buttons('translate_form'));
    }

    public function addInputFilters()
    {
        $filter = $this->getInputFilter();
        $langsContent = $filter->get('langs-content');

        $fields = $this->localizableContent['fields'];

        foreach ($langsContent->getInputs() as $langGroup) {
            foreach ($langGroup->getInputs() as $input) {
                $name = $input->getName();
                if (isset($fields[$name])) {
                    $input->getFilterChain()->attach(new StringTrim());
                    if ($fields[$name]['type'] == 'Text')
                        $input->getFilterChain()->attach(new StripTags());
                }
            }
        }
    }
}
