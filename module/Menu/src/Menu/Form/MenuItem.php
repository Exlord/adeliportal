<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace Menu\Form;

use Menu\Form\Fieldset\Mega;
use System\Form\BaseForm;
use Zend\Captcha;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Form\Factory;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;
use Zend\Validator\StringLength;
use Zend\Filter;

class MenuItem extends BaseForm implements InputFilterProviderInterface
{
    private $parentId;
    private $menuId;
    protected $loadInputFilters = false;
    public $menuTypes = array();

    /**
     * @param null $menuId
     * @param array $parentId
     */
    public function __construct($menuId, $parentId)
    {
        $this->parentId = $parentId;
        $this->menuId = $menuId;
        $this->setAttribute('class', 'normal-form ajax_submit');
        parent::__construct('menu_item');
        /* @var $menu_api \Menu\API\Menu */
        $menu_api = getSM()->get('menu_api');
        $menu_api->LoadMenuTypes($this);
        $itemUrlTypeParams = $this->get('itemUrlTypeParams');
        foreach ($this->menuTypes as $name => $params) {
            $fieldset = new Fieldset($name);
            $fieldset->setLabel($params['label']);
            $fieldset->setAttribute('id', $name);
            if (isset($params['note']))
                $fieldset->setOptions(array('description' => $params['note']));

            if (isset($params['fields'])) {
                foreach ($params['fields'] as $field) {
                    $fieldset->add($field);
                }
            }
            if (isset($params['params'])) {
                $paramsFieldset = new Fieldset('params');
                foreach ($params['params'] as $field) {
                    $fieldName = $field;
                    $value = '';
                    if (is_array($field)) {
                        $fieldName = key($field);
                        $value = current($field);
                    }
                    $paramsFieldset->add(array(
                        'type' => 'Zend\Form\Element\Hidden',
                        'name' => $fieldName,
                        'attributes' => array(
                            'data-field' => $fieldName,
                            'value' => $value,
                        )
                    ));
                }
                $fieldset->add($paramsFieldset);
            }
            if (isset($params['data-url'])) {
                $fieldset->add(array(
                    'name' => 'data-loader', //static name for all auto-complete lists
                    'type' => 'Zend\Form\Element\Text',
                    'options' => array(
                        'label' => 'Search',
                        'description' => 'Start typing to load a list of matched inputs.<br/>' .
                            "<span dir='ltr' class='left-align'>_ (underline)</span> : will match any character.<br/>" .
                            "<span dir='ltr' class='left-align'>__ (2 underlines)</span> : will load a list of all available inputs."
                    ),
                    'attributes' => array(
                        'size' => 50,
                        'class' => 'data-loader',
                        'data-url' => $params['data-url'],
                        'data-type' => $name,
                        'data-template' => $params['template']
                    )
                ));
            }
            $itemUrlTypeParams->add($fieldset);
        }
        $this->addInputFilters();
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'itemUrlType',
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'id' => 'itemUrlType'
            )
        ));

        $this->add(array(
            'name' => 'menuId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Menu',
                'empty_option' => '-- Select --',
                'value_options' => $this->menuId
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'parentId',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Parent Menu Item',
                'empty_option' => '-- Select --',
                'value_options' => $this->parentId
            ),
            'attributes' => array(
                'value' => 0,
                'class' => 'select2',
            )
        ));

        $this->add(array(
            'name' => 'itemName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Menu Item Name'
            ),
        ));

        $this->add(array(
            'name' => 'itemTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Menu Item Title'
            ),
        ));

        $urlTypeParams = new Fieldset('itemUrlTypeParams');
        $this->add($urlTypeParams);

        $config = new Fieldset('config');
        $config->add(new Mega());
        $config->add(new \Menu\Form\Fieldset\Options());
        $this->add($config);

        $this->add(array(
            'name' => 'status',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Is Enabled ?'
            ),
        ));

        $this->add(array(
            'name' => 'itemOrder',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Order'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => -999,
                'data-max' => 999,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'name' => 'image',
            'type' => 'Zend\Form\Element\File',
            'options' => array(
                'label' => 'Image',
                'description' =>
                    'Allowed extensions are jpg, jpeg, png and gif<br/>' .
                    'Min size : 100x100 and Max size : 1500x1500<br/>' .
                    'Max file size : 1MB' .
                    'The image size does not change',
            ),
        ));

        $this->add(new \System\Form\Buttons('menu_item'));
    }

    protected function addInputFilters()
    {
        /**
         * @var $parentId \Zend\InputFilter\Input
         */
        $filter = $this->getInputFilter();
        $parentId = $filter->get('parentId')->setRequired(false);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'itemUrlType' => array(
                'required' => true,
            ),
            'parentId' => array(
                'name' => 'parentId',
                'required' => false,
            ),
            'image' => array(
                'name' => 'image',
                'required' => false,
                'validators' => array(
                    new Extension('jpg,jpeg,png,gif'),
                    new MimeType('image'),
                )
            )
        );
    }
}
