<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/29/14
 * Time: 11:40 AM
 */

namespace Theme\View\Helper;


use System\View\Helper\BaseHelper;

class Partial extends BaseHelper
{
    public function __invoke($block)
    {
        $block->data['class'] .= ' custom-file-block';
        $block->blockId = 'custom-file-block-' . $block->id;
        return $this->view->partial($block->data['custom_template_file']['template']);
    }
} 