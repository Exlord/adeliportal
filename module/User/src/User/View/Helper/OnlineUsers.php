<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/18/12
 * Time: 3:12 PM
 */

namespace User\View\Helper;

use System\View\Helper\BaseHelper;
use User\Form;
use User\Model;
use Zend\Db\Adapter\Adapter;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class OnlineUsers extends BaseHelper
{
    public function __invoke($block)
    {
        /* @var $adapter Adapter */
        $adapter = getSM('db_adapter');
        $time = strtotime('-5 minutes');
        $q = 'select count(*) as count from tbl_session where modified >' . $time;
        $statement = $adapter->query($q);
        $result = $statement->execute()->current();
        $temp = sprintf(t('We have %s online user'),$result['count']);
        $block->blockId = 'online-users';
        return $temp;
    }
}
