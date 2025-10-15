<?php
namespace Application\View\Helper;


use System\View\Helper\BaseHelper;

class CustomBlock extends BaseHelper
{
    public function __invoke($block)
    {
        $customText = $block->data[$block->type]['customText'];
//        $btnExit = $block->data[$block->type]['btnExit'];
        $type = $block->data[$block->type]['type'];

        $block->data['class'] .= ' custom-html-block';
        $block->blockId = 'custom-html-block-' . $block->id;

        if ($type == 'custom_block') {
            return $this->view->render('application/block/custom', array(
                'text' => $customText,
//                'btnExit' => $btnExit,
            ));
        }
    }
}
