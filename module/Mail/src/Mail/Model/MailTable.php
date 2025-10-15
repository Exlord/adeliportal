<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Mail\Model;


use System\DB\BaseTableGateway;

class MailTable extends BaseTableGateway
{
    protected $table = 'tbl_mail_queue';
    protected $model = 'Mail\Model\Mail';
    protected $caches = null;
    protected $cache_prefix = null;

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct($this->table, $adapter);
    }

    public function getMails()
    {
        $max = getSM('mail_send_table')->getRemainingCount();
        $q = "SELECT
                   `tbl_mail_queue`.*
                FROM
                   `tbl_mail_queue`
                    LEFT JOIN
                        (SELECT
                             id,
                             @sum:=@sum+`count` AS current_sum
                            FROM
                              `tbl_mail_queue`
                              CROSS JOIN(SELECT @sum:=0) AS init
                              WHERE `tbl_mail_queue`.status = 0 AND `tbl_mail_queue`.domain = ?
                              ORDER BY `tbl_mail_queue`.queued ASC) AS sums
                         ON `tbl_mail_queue`.id=sums.id
                WHERE sums.current_sum<=?
                ";
        return $this->adapter->query($q, array(ACTIVE_SITE, $max))->toArray();
    }
}
