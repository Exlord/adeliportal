<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Mail\Controller;

use Application\Model\Config;
use DataView\Lib\Column;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use DataView\Lib\Visualizer;
use Mail\Model\MailArchiveTable;
use Mail\Model\MailTable;
use System\Controller\BaseAbstractActionController;
use Zend\View\Model\JsonModel;

class CronController extends BaseAbstractActionController
{
   public function sendAction(){
       /* @var $api \Mail\API\Mail */
       $api = getSM('mail_api');
       return $api->send();
   }
}
