<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/1/14
 * Time: 12:58 PM
 */

namespace Ads;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_154(){
        $q = "ALTER TABLE `tbl_ads_order` ADD `isRequest` TINYINT NOT NULL DEFAULT '0';";
        $this->db->query($q)->execute();
    }

} 