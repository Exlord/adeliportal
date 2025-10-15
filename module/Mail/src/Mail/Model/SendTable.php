<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Mail\Model;


use Mail\API\Mail as MailApi;
use System\DB\BaseTableGateway;

class SendTable extends BaseTableGateway
{
    protected $table = 'tbl_send_count';
    protected $model = 'Mail\Model\Send';
    protected $caches = null;
    protected $cache_prefix = null;
    private $sendTime;

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct($this->table, $adapter);
    }

    public function getRemainingCount()
    {
        $h = date('G');
        $m = date('n');
        $d = date('j');
        $y = date('Y');
        $time = mktime($h, 0, 0, $m, $d, $y);
        $this->sendTime = $time;
        $result = $this->select(array('sendTime' => $time, 'domain' => ACTIVE_SITE));
        if ($result->count()) {
            return MailApi::MAX_PER_HOUR - (int)$result->current()->sendCount;
        } else {
            $this->insert(array('sendTime' => $time, 'sendCount' => 0, 'domain' => ACTIVE_SITE));
            return MailApi::MAX_PER_HOUR;
        }
    }

    public function setCount($count)
    {
        if (!$this->sendTime) {
            $h = date('G');
            $m = date('n');
            $d = date('j');
            $y = date('Y');
            $time = mktime($h, 0, 0, $m, $d, $y);
            $this->sendTime = $time;
        }
        $this->update(array('sendCount' => new \Zend\Db\Sql\Expression('sendCount + ' . $count)), array('sendTime' => $this->sendTime, 'domain' => ACTIVE_SITE));
    }
}
