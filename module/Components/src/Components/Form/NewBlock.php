<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/21/13
 * Time: 2:57 PM
 */
namespace Components\Form;

use System\Form\BaseForm;
use System\Form\Buttons;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class NewBlock extends BaseForm implements InputFilterProviderInterface
{
    private $_positions;
    private $_patchSamples;
//    private $_domainSample;
    protected $loadInputFilters = false;
    public $extraScripts = array();
    public $extraInlineScripts = array();
    public $extraStyles = array();
    private $domains = false;

    public function __construct($blockType, $positions, $patchSamples)
    {
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAttribute('data-cancel', url('admin/block'));
        $this->_positions = $positions;
        $this->_patchSamples = $patchSamples;
        parent::__construct('new_block');
        /* @var $menu_api \Components\API\Block */
        $menu_api = getSM()->get('block_api');
        $menu_api->LoadBlockConfigs($this, $blockType);
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'type',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Block Title',
                'description' => 'The title of the block as shown to the user.'
            )
        ));
        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Block description',
                'description' => 'A brief description of your block. Used on the Blocks administration page.'
            ),
        ));
        $this->add(array(
            'name' => 'position',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'empty_option' => '-- Select --',
                'label' => 'Position',
                'value_options' => $this->_positions,
                'description' => 'Select in witch area of the template this block should be shown'
            ),
            'attributes' => array(
                'class' => 'select2',
            )
        ));
        $this->add(array(
            'name' => 'enabled',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Is Enabled ?',
                'description' => ''
            )
        ));

        $this->add(array(
            'name' => 'order',
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
            'name' => 'visibility',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => 'Show block on specific pages',
                'value_options' => array(
                    '0' => 'All pages except those listed',
                    '1' => 'Only the listed pages'
                ),
                'description' => ''
            )
        ));

        $this->add(array(
            'name' => 'pages',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'dir' => 'ltr',
                'cols' => 50,
                'rows' => 5,
                'class' => 'left-align',
                'data-tooltip' => $this->_patchSamples,
            ),
            'options' => array(
                'description' => "Specify pages by using their paths. Enter one path per line. The '*' character is a wildcard."
            )
        ));

        if (getSM()->has('domain_table')) {
            $this->domains = getSM('domain_table')->getArray();
            if (count($this->domains)) {
//                $this->domains =  $this->domains;
                $this->add(array(
                    'name' => 'domains',
                    'type' => 'Zend\Form\Element\Select',
                    'attributes' => array(
                        'class' => 'select2',
                        'multiple' => 'multiple',
                        'data-placeholder' => '-- All --'
                    ),
                    'options' => array(
                        'empty_option' => '-- All --',
                        'label' => 'Display in Domains',
                        'description' => "this block will only be displayed in the selected domains",
                        'value_options' => $this->domains,
                        'disable_inarray_validator' => true
                    )
                ));
            }
        }

        $blockConfigs = new Fieldset('data');
        $blockConfigs->setLabel('Settings');
        $this->add($blockConfigs);

        $blockConfigs->add(array(
            'name' => 'class',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Css Class',
                'description' => 'A css class name to be added to the blocks html markup.'
            ),
        ));

        $this->add(new Buttons('new_block', array(Buttons::SAVE, Buttons::SAVE_NEW, Buttons::SAVE_CLOSE, Buttons::CSRF, Buttons::SPAM, Buttons::CANCEL)));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $input = array();
        if ($this->domains) {
            $input['domains'] = array(
                'required' => false,
                'allow_empty' => true,
            );
        }
        return $input;
    }
}