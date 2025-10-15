<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/1/14
 * Time: 4:20 PM
 */

namespace Application\Form;


use System\Form\BaseForm;
use Zend\InputFilter\Input;

class Search extends BaseForm
{
    public function __construct()
    {
        parent::__construct('search');
        $this->setAttribute('class', 'search-form');
        $this->setAction(url('app/search'));
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'keyword',
            'options' => array(
                'label' => 'Search'
            ),
            'attributes' => array(
                'placeholder' => t('Search')
            )
        ));

        $this->add(array(
            'type' => 'System\Form\Element\SpamGuard',
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'search',
            'attributes' => array(
                'value' => 'Search',
                'class' => 'button search_button',
            )
        ));
    }

    protected function addInputFilters()
    {
        /* @var $keyword Input */
        $keyword = $this->getInputFilter()
            ->get('keyword');
        $keyword
            ->setRequired(true)
            ->setAllowEmpty(false)
            ->getFilterChain()
            ->attach(new \Zend\Filter\StringTrim())
            ->attach(new \Zend\Filter\StripTags());
    }
}