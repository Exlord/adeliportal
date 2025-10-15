<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Barcode\Controller;

use System\Controller\BaseAbstractActionController;
use Zend\Barcode\Barcode;
use \Zend\Mvc\Controller\AbstractActionController;
use \Zend\View\Model\ViewModel;
use DataView\API\Grid;
use DataView\API\GridColumn;

class BarcodeController extends BaseAbstractActionController
{
    public function getAction()
    {
        $code = $this->params()->fromRoute('barcode', 0);

        $barcodeOptions = array('text' => $code);

        $rendererOptions = array();
        Barcode::render(
            'code39', 'image', $barcodeOptions, $rendererOptions
        );
    }
}
