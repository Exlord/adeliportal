<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/11/13
 * Time: 1:49 PM
 */

namespace Localization\Form;


use Zend\Form\Fieldset;

class Lang extends Fieldset
{
    public $sign;

    public function __construct($langSign, $fields, $pkFiled = 'id')
    {
        $this->sign = $langSign;
        parent::__construct($langSign);

        $this->add(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id',
        ));

        foreach ($fields as $name => $props) {
            if (isset($props['visible']) && $props['visible'] == false)
                continue;
//            $this->add(new Element($langSign, $name, $props));

            $el = array(
                'type' => 'Zend\Form\Element\\' . $props['type'],
                'name' => $name,
                'options' => array(
                    'label' => $props['label']
                )
            );
            if ($props['type'] == 'Textarea') {
                if (isset($props['editor']) && $props['editor']) {
                    $el['attributes']['class'] = 'editor';
                    $el['attributes']['id'] = $langSign . '_' . $name;
                } else {
                    if (isset($props['cols']))
                        $el['attributes']['cols'] = $props['cols'];
                    else
                        $el['attributes']['cols'] = 75;

                    if (isset($props['rows']))
                        $el['attributes']['rows'] = $props['rows'];
                    else
                        $el['attributes']['rows'] = 5;
                }
            }

            if ($props['type'] == 'Text') {
                if (isset($props['size']))
                    $el['attributes']['size'] = $props['size'];
                else
                    $el['attributes']['size'] = 75;
            }

            $this->add($el);
        }
    }
} 