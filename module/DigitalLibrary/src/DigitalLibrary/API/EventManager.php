<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 11:51 AM
 */

namespace DigitalLibrary\API;


use Application\API\App;
use Application\Model\Config;
use Components\Form\NewBlock;
use Cron\API\Cron;
use SiteMap\Model\Url;
use Theme\API\Common;
use Zend\EventManager\Event;
use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Select;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;

class EventManager
{
    public function OnSiteMapGeneration(Event $e)
    {
        $sitemap = $e->getParam('sitemap');
        $config = $e->getParam('config');

        if (isset($config['DigitalLibrary'])) {
            $config = $config['DigitalLibrary'];
            if (isset($config['books'])) {
                if (isset($config['books']['xml']) && $config['books']['xml'] == '1') {
                    $url = App::siteUrl() . url('app/books');
                    $sitemap->addUrl(new Url($url));

                    $data = getSM('book_table')->getAll();
                    foreach ($data as $row) {
                        $url = App::siteUrl() . url('app/books/view',
                                array('id' => $row->id, 'title' => App::prepareUrlString($row->title)));
                        $sitemap->addUrl(new Url($url));
                    }
                }
                if (isset($config['books']['html']) && $config['books']['html'] == '1') {
                    $sitemap->tree['/']['children']['DigitalLibrary']['data'] = Common::Link(t('Books'), url('app/books'));
                }
            }
        }
    }

    public function onLoadBlockConfigs(Event $e)
    {
        $type = $e->getParam('type');

        if ($type == 'dl_search') {

            /* @var $form NewBlock */
            $form = $e->getParam('form');
            $dataFieldset = $form->get('data');

            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('Digital Library Search Settings');
            $dataFieldset->add($fiedlset);

            $table = new Fieldset('table');
            $fiedlset->add($table);
            $field = new Fieldset('field');
            $fiedlset->add($field);

            $title = new Checkbox('title');
            $title->setLabel('Title');
            $table->add($title);

            $fields = getSM('fields_table')->getByEntityType('books');
            foreach ($fields as $f) {

                $name = $f->id . ',' . $f->fieldMachineName;
//                switch ($f->fieldType) {
////                    case 'text':
////                        $el = new Select($name);
////                        $el->setValueOptions(array(
////                            '0' => 'None',
////                            'text' => 'Normal TextBox',
////                            'spinner' => 'Spinner TextBox',
////                            'slider' => 'Slider'
////                        ));
////                        break;
//                    default:
//
//                        break;
//                }
                $el = new Checkbox($name);
                $el->setLabel($f->fieldName);
                $field->add($el);
            }
        }
    }
} 