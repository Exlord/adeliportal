<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/22/12
 * Time: 10:51 AM
 */
namespace System\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class Fieldset extends \System\View\Helper\BaseHelper
{
    /**
     * @param $fieldset \Zend\Form\Fieldset
     * @return string
     */
    public function __invoke($fieldset)
    {
        $fieldset_template = "<fieldset><legend>%s</legend>%s</fieldset>";
        $row_template = "<div class='form_element'>%s</div>";
        $label = $fieldset->getLabel();
        if ($label)
            $label = $this->view->translate($label);

        $html = '';
        foreach ($fieldset->getElements() as $el) {
            if ($el instanceof \Zend\Form\Fieldset) {
                $html .= $this->view->fieldset($el);
            } else {

                $html .= sprintf($row_template, $this->view->iptFormRow($el));
            }
        }
        return sprintf($row_template, sprintf($fieldset_template, $label, $html));
    }
}
