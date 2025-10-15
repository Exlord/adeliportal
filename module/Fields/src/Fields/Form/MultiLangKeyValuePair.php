<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 6/10/14
 * Time: 9:41 AM
 */

namespace Fields\Form;


use Zend\Form\Fieldset;

class MultiLangKeyValuePair extends Fieldset
{
    public function __construct($count = 1)
    {
        parent::__construct('keyValuePairs');
        $this->setLabel('Kay-Value Pairs');
        $languages = getSM('language_table')->getArray(true);
        foreach ($languages as $lSign => $lName) {
//            $langFieldset = new Fieldset($lSign);
//            $langFieldset->setLabel($lName);
            $this->add(array(
                'type' => 'Zend\Form\Element\Collection',
//                'name' => 'keyValuePairs',
                'name' => $lSign,
                'options' => array(
//                    'label' => 'Kay-Value Pairs',
                    'label' => $lName,
                    'count' => $count,
                    'should_create_template' => true,
                    'allow_add' => true,
                    'target_element' => array(
                        'type' => 'Fields\Form\KeyValuePairField'
                    )
                ),
                'attributes' => array(
                    'class' => 'collection-container'
                ),
            ));
//            $this->add($langFieldset);
        }
    }
} 