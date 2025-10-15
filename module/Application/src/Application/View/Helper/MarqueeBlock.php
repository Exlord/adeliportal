<?php
namespace Application\View\Helper;


use System\View\Helper\BaseHelper;

class MarqueeBlock extends BaseHelper
{
    public function __invoke($block)
    {
        $marqueeText = $block->data[$block->type]['marquee-text'];
        $type = $block->data[$block->type]['type'];

        $galleryName = 'marquee-block-' . $block->id;
        if (!isset($block->data['class']))
            $block->data['class'] = 'marquee-block';
        $block->blockId = $galleryName;
        if ($type == 'marquee') {
            return $this->view->render('application/block/marquee', array(
                'text' => $marqueeText,
            ));
        }

    }
}
