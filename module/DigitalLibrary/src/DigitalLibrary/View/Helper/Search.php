<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/12/14
 * Time: 10:40 AM
 */

namespace DigitalLibrary\View\Helper;


use System\View\Helper\BaseHelper;

class Search extends BaseHelper
{
    public function __invoke($block)
    {
        $block->blockId = 'digital-library-search-' . $block->id;
        $block->data["class"] .= " digital-library-search";

        $request = getSM('Request');
        $data = array_merge_recursive($request->getQuery()->toArray(), $request->getPost()->toArray());
        $form = new \DigitalLibrary\Form\Search($block);
        $form->setData($data);
        $form->isValid();

        return $this->view->render('digital-library/helper/search',array(
            'block' => $block,
            'form' => $form
        ));
    }
} 