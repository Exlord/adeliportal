<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/12/14
 * Time: 12:43 PM
 */

namespace DigitalLibrary\Form;


use Components\Model\Block;
use RealEstate\Model\RealEstateTable;
use System\Form\BaseForm;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class Search extends BaseForm implements InputFilterProviderInterface
{
    protected $loadInputFilters = false;
    /**
     * @var Block
     */
    private $block;
    public $static_fields_data;
    /**
     * @var \Zend\Http\PhpEnvironment\Request
     */
    private $request;

    public function __construct($block)
    {
        $this->block = $block;
        $this->request = getSM('Request');
        parent::__construct('digital-library-search');
        $this->setAttribute('action', url('app/books'));
        $this->setAttribute('method', 'GET');
    }

    protected function addElements()
    {
        $table = new Fieldset('table');
        $fieldset = new Fieldset('field');
        $this->add($table);
        $this->add($fieldset);

        foreach ($this->block->data['dl_search']['table'] as $field => $value) {
            if ($value != '0') {
                switch ($field) {
                    case 'title':
                        $table->add(array(
                            'type' => 'Text',
                            'name' => $field,
                            'options' => array(
                                'label' => 'Book Title',
                            ),
                            'attributes' => array(
                                'placeholder' => 'Book Title'
                            )
                        ));
                        break;
                }
            }
        }

        $fieldIds = array();
        $fields = array();
        foreach ($this->block->data['dl_search']['field'] as $field => $value) {
            if ($value != '0') {
                $fieldParts = explode(',', $field);
                $fieldIds[] = $fieldParts[0];
            }
        }

        /* @var $fieldsApi \Fields\API\Fields */
        $fieldsApi = getSM()->get('fields_api');
        $fieldsTable = $fieldsApi->init('real_estate');
        $fieldsApi->loadFieldsById($fieldIds, $this, $fieldset);

        $this->add(array(
            'type' => 'Button',
            'name' => 'search',
            'options' => array(
                'glyphicon' => 'search text-primary fa-lg',
                'label' => '<span class="form-button-text">' . t('Search') . '</span>',
                'label_options' => array(
                    'disable_html_escape' => true
                ),
            ),
            'attributes' => array(
                'class' => 'btn btn-default',
                'type' => 'submit',
                'title' => t('Click here to Search'),
                'data-toggle' => 'tooltip',
                'data-placement' => 'bottom'
            )
        ));
    }


    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $filter = array();
        foreach ($this->getFieldsets() as $fieldset) {
            foreach ($this->getElements() as $el) {
                $filter[$fieldset->getName()][$el->getName()] = array(
                    'name' => $el->getName(),
                    'required' => false,
                    'allow_empty' => true
                );
            }
        }

        return $filter;
    }
}