<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/1/14
 * Time: 4:19 PM
 */

namespace Application\View\Helper;


use System\View\Helper\BaseHelper;

class Search extends BaseHelper
{
    public function __invoke($block = null)
    {
        if ($block) {
            $block->data['class'] = 'search-block';
            $block->blockId = 'search-block-' . $block->id;

            if (!$this->loaded) {
                $this->view->headScript()->appendFile($this->view->basePath() . '/js/search.js');
                $this->view->headLink()->appendStylesheet($this->view->basePath() . '/css/search.css');
            }
            $this->loaded = true;

            $atVP = (isset($block->data['search_block']) && isset($block->data['search_block']['open_position_vertical'])) ?
                $block->data['search_block']['open_position_vertical'] :
                'bottom';

            $myVp = $atVP == 'bottom' ? 'top' : 'bottom';

            $position_horizontal = isset($block->data['search_block']) && isset($block->data['search_block']['open_position_horizontal']) && $block->data['search_block']['open_position_horizontal'] != 'default';
            $hP = ($position_horizontal) ?
                $block->data['search_block']['open_position_horizontal'] : (t('lang_direction') == 'rtl' ? 'right' : 'left');

            return $this->view->render('application/helper/search',
                array('id' => $block->id,
                    'position' => '{ my: "' . $hP . ' ' . $myVp . '", at: "' . $hP . ' ' . $atVP . '"}'));
        } else
            return $this;
    }
} 