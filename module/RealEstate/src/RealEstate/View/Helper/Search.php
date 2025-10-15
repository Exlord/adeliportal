<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/12/14
 * Time: 10:40 AM
 */

namespace RealEstate\View\Helper;


use System\View\Helper\BaseHelper;

class Search extends BaseHelper
{
    public function __invoke($block)
    {
        $block->blockId = 'real-estate-search-block' . $block->id;
        $block->data["class"] .= " real-estate-search-block";
        $static_fields_data = array();

        $request = getSM('Request');
        $data = array_merge_recursive($request->getQuery()->toArray(), $request->getPost()->toArray());
        $form = new \RealEstate\Form\Search($block);
        $form->setData($data);
        $form->isValid();

        return $this->view->render('real-estate/helper/search',array(
            'block' => $block,
            'static_fields_data' => $static_fields_data,
            'form' => $form
        ));
    }
} 