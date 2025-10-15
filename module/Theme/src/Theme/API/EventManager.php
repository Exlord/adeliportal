<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 4:19 PM
 */

namespace Theme\API;


use Components\Form\NewBlock;
use System\IO\Directory;
use Zend\EventManager\Event;
use Zend\Form\Fieldset;

class EventManager {
    public function onLoadBlockConfigs(Event $e)
    {
        $type = $e->getParam('type');

        if ($type == 'custom_template_file') {

            /* @var $form NewBlock */
            $form = $e->getParam('form');
            $dataFieldset = $form->get('data');

            // $blockInfo = Block::getBlockInfo($type);
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('Custom Template File');
            $dataFieldset->add($fiedlset);

            $templateFiles = array();
            $theme = Themes::getClientTheme();
            $theme = $theme->name;
            $templatesDire = ROOT . '/module/Theme/public/themes/' . $theme . '/templates/theme/custom';
            if (is_dir($templatesDire))
                $templateFiles = Directory::getFiles($templatesDire);

            $templates = array();
            if ($templateFiles && count($templateFiles)) {
                foreach ($templateFiles as $file) {
                    $templates['theme/custom/' . $file] = $file;
                }
            }

            $fiedlset->add(array(
                'name' => 'template',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Template File',
                    'empty_option' => '-- Select --',
                    'value_options' => $templates,
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
        }
    }
} 