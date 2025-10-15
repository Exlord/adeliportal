<?php
/**
 * Created by PhpStorm.
 * User: Ajami
 * Date: 9/29/14
 * Time: 12:11 AM
 */

namespace Ads\Form\FilterFields;


use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Stdlib\PriorityQueue;

class Type extends Fieldset
{
    public function __construct($selectedFields, $fields)
    {
        $key = key($selectedFields);
        $type = $selectedFields[$key];
        unset($selectedFields[$key]);

        parent::__construct($key);
        $this->setLabel($type['label']);
        if (count($selectedFields)) {
            foreach ($type['items'] as $key => $val) {
                $item = new Fieldset($key);
                $item->setLabel($val);
                $this->add($item);
                $innerType = new Type($selectedFields, $fields);
                //$innerType->setLabel($val);
                $item->add($innerType);
            }
        } else {
            foreach ($type['items'] as $key => $val) {
                $checkboxes = new Fieldset($key);
                $checkboxes->setLabel($val);
                $this->add($checkboxes);
                foreach ($fields as $key2 => $name) {
                    $checkboxes->add(array(
                        'name'=>$key2,
                        'type'=>'Checkbox',
                        'options'=>array(
                            'label'=>$name,
                        )
                    ));
                }
            }
        }
    }


}