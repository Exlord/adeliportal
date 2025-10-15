<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/15/12
 * Time: 9:17 AM
 */

namespace Components\View\Helper;

use Application\API\App;
use Components\Model\BlockTable;
use Components\Model\UrlTable;
use System\View\Helper\BaseHelper;
use Components\Model\Block;

class Blocks extends BaseHelper
{
    const ALL_BUT_MATCHED = '0';
    const JUST_MATCHED = '1';

    private $components = null;
    private $blocks = null;

    public function __invoke($position = null, $template = 'block')
    {
        $this->getBlocks();
        $block_content = '';

        if (isset($this->blocks[$position]) && count($this->blocks[$position])) {
            //----------------- Blocks in position --------------------
            /* @var $bl \Components\Model\Block */
            foreach ($this->blocks[$position] as $id => $bl) {
                $bl->unserializeData();
//                $domainMatch = App::matchDomain($bl->domains, $bl->domainVisibility);
//                if ($domainMatch) {
                if (!isset($bl->data['class']))
                    $bl->data['class'] = '';

                $helper = $this->components[$bl->type]['helper'];

                $block = $this->view->{$helper}($bl);
                $bl->content = $block;
                if (!isset($bl->blockId) || empty($bl->blockId))
                    return $this->view->alert('error: blockId is not set. $block->blockId = "[your block name]-block-" . $block->id;', array('class' => 'alert-danger', 'dir' => 'ltr'));

                if (!isset($bl->data['class']) || empty($bl->data['class']))
                    return $this->view->alert('error: define a class for your block,exp: $block->data["class"] .= " [your block name]-block";', array('class' => 'alert-danger', 'dir' => 'ltr'));

                $bl->data['class'] = trim($bl->data['class']);

                if ($template) {
                    $block = $this->view->render('components/blocks/' . $template,
                        array('block' => $bl));
                }
                $block_content .= $block;
//                }
            }
            $block_content = trim($block_content);
        }
        return !empty($block_content) ? $block_content : false;
    }


    /**
     * @return BlockTable
     */
    private function getBlockTable()
    {
        return getSM('block_table');
    }

    /**
     * @return UrlTable
     */
    private function getBlockUrlTable()
    {
        return getSM('block_url_table');
    }

    private function getComponents()
    {
        if ($this->components == null) {
//            $components = getSM('Config');
//            $this->components = $components['components'];
            $this->components = getSM('block_api')->LoadBlockTypes();
        }
        return $this->components;
    }

    private function getBlocks()
    {
        if (is_null($this->blocks)) {
            $this->blocks = array();
            $this->getComponents();
            $uri = getSM('Request')->getRequestUri();
            $host = getSM('Request')->getUri()->getHost();
            $blockIds = $this->getBlockUrlTable()->get($host . $uri);
            $blocks = $this->getBlockTable()->getBlocks(null, $blockIds);

            if (is_null($blockIds) && $blocks && count($blocks)) {
                $blockIds = array();
                foreach ($blocks as $position => $row) {
                    /* @var $bl \Components\Model\Block */
                    foreach ($row as $id => $bl) {
                        $match = App::matchPath($bl->pages, $bl->visibility);
                        if ($match) {
                            $blockIds[] = $id;
                            $this->blocks[$position][$id] = $bl;
                        }
                    }
                }
                $this->getBlockUrlTable()->save(array('url' => $host . $uri, 'blocks' => serialize($blockIds)));
            } else
                $this->blocks = $blocks;
            $blocks = null;
        }
    }
}
