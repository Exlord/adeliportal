<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/2/14
 * Time: 2:05 PM
 */

namespace RSS;


use System\DB\BaseInstall;

class Install extends BaseInstall{

    public function update_117(){
        $q = "ALTER TABLE `tbl_newsletter_sign_up` ADD INDEX ( `status` ) ;";
        $q .= "ALTER TABLE `tbl_rss_reader` ADD INDEX ( `status` ) ;";
        $this->db->query($q)->execute();
    }
} 