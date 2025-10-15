<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 4:05 PM
 */

namespace RSS\API;


use Components\API\Block;
use Components\Form\NewBlock;
use Cron\API\Cron;
use RSS\Model\ReaderTable;
use Zend\EventManager\Event;
use Zend\Form\Fieldset;

class EventManager
{
    public function onCronRun(Event $e)
    {
        $last_run = $e->getParam('last_run');

        $start = microtime(true);
        $readers = getSM('rss_reader_table')->getAll(array('status' => 1));

        if ($readers && $readers->count()) {
            /* @var $reader \RSS\Model\Reader */
            foreach ($readers as $reader) {
                if (Cron::ShouldRun('+ ' . ReaderTable::$readInterval[$reader->readInterval], $reader->lastRead)) {
                    try {
                        Reader::read($reader);
                        $last_run->varValue['RSS_READER_' . $reader->id] = time();
                        db_log_info(sprintf(t('%s RSS Reader updated throw cron run in %s seconds at %s'),
                            $reader->title, Cron::GetRunTime($start), dateFormat(time(), 0, 0)));
                    } catch (\Exception $e) {
                        db_log_exception($e);
                    }
                }
            }
        }
    }

    public function onLoadBlockConfigs(Event $e)
    {
        $type = $e->getParam('type');

        if ($type == 'rss_reader_block') {

            /* @var $form NewBlock */
            $form = $e->getParam('form');
            $dataFieldset = $form->get('data');

            $blockInfo = Block::getBlockInfo($type);
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('RSS Reader Settings');
            $dataFieldset->add($fiedlset);

            $menus = getSM('rss_reader_table')->getArray();
            $fiedlset->add(array(
                'name' => 'feedId',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'RSS Feeds',
                    'value_options' => $menus,
                    'description' => 'Select the rss fedd you want to be fetched and displayed in this block.'
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));
        }
    }
} 