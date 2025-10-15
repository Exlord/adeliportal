<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/8/14
 * Time: 2:03 PM
 */

namespace Logger;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_12()
    {
        $q = "RENAME TABLE tbl_event_loges TO tbl_event_logs";
        $this->db->query($q)->execute();
        $q = "alter table `tbl_event_logs` add column `entityType` varchar(200) NOT NULL";
        $this->db->query($q)->execute();
    }

    public function update_131(){
        $q = "alter table `tbl_event_logs` MODIFY COLUMN `entityType` varchar(200) NULL";
        $this->db->query($q)->execute();
    }

    public function update_136()
    {
        $q = "ALTER TABLE `tbl_event_logs` ADD INDEX ( `entityType` ) ;";
        $this->db->query($q)->execute();
        $q = "ALTER TABLE `tbl_event_logs` ADD INDEX ( `priority` ) ;";
        $this->db->query($q)->execute();
        $q = "ALTER TABLE `tbl_event_logs` ADD INDEX ( `timestamp` ) ;";
        $this->db->query($q)->execute();
    }
} 