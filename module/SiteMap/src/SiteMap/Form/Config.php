<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/24/14
 * Time: 10:20 AM
 */

namespace SiteMap\Form;


use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class Config extends BaseForm implements InputFilterProviderInterface
{

    public function __construct()
    {
        parent::__construct('sitemap_config');
        $this->setAction(url('admin/sitemap'));
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {
        $config = getSM('sitemap_api')->getConfig();

        $modules = new Fieldset('modules');
        $modules->setOption('description', 'select witch section of witch module should be included in the sitemap');
        $this->add($modules);
        foreach ($config as $moduleName => $section) {

            $module = new Fieldset($moduleName);
            $module->setLabel($moduleName);
            $modules->add($module);

            foreach ($section as $name => $title) {

                $section = new Fieldset($name);
                $section->setLabel($title);
                $module->add($section);

                $section->add(array(
                    'type' => 'Checkbox',
                    'name' => 'xml',
                    'options' => array(
                        'label' => ''
                    )
                ));
                $section->add(array(
                    'type' => 'Checkbox',
                    'name' => 'html',
                    'options' => array(
                        'label' => ''
                    )
                ));

                $section->add(array(
                    'type' => 'Text',
                    'name' => 'freq',
                    'options' => array(
                        'label' => ''
                    )
                ));
            }
        }

        $this->add(new Buttons());
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array();
    }
}