<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/26/13
 * Time: 9:30 AM
 */

namespace Application\API;


use System\API\BaseAPI;
use Zend\View\Model\ViewModel;

class Printer extends BaseAPI
{
    /**
     * @param $content
     * @param null $template
     * @return ViewModel
     */
    public static function getViewModel($content, $template = null)
    {
        if ($template) {
            $content = App::RenderTemplate($template, $content);
        }
        $viewModel = new ViewModel(array('page' => $content));
        $viewModel->setTemplate('application/index/print')
            ->setTerminal(true);
        return $viewModel;
    }
} 