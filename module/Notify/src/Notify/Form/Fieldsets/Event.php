<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/23/14
 * Time: 11:14 AM
 */

namespace Notify\Form\Fieldsets;


use Zend\Form\Fieldset;

class Event extends Fieldset
{
    public function __construct($nKey, $params, $templates, $inherited = false)
    {
        parent::__construct($nKey);
        $this->setLabel($params['label']);
        if (isset($params['description']))
            $this->setOptions(array('description' => $params['description']));

        $notifyWith = $params['notify_with'];
        if (!is_array($notifyWith))
            $notifyWith = array($notifyWith);

        foreach ($notifyWith as $nT => $template) {
            $this->add(new NotifyWith($nT, $template, $templates, $inherited));
        }
    }
} 