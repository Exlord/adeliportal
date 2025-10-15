<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/5/14
 * Time: 10:31 AM
 */

namespace Links;


use System\DB\BaseInstall;

class Install extends BaseInstall{
    public function update_124(){
        $q = "ALTER TABLE `tbl_links_items` ADD INDEX ( `itemOrder` );";
        $q .= "ALTER TABLE `tbl_links_items` ADD INDEX ( `itemStatus` );";
        $q .= "ALTER TABLE `tbl_links_items` ADD INDEX ( `catId` );";
        $q .= "ALTER TABLE `tbl_links_items` ADD INDEX ( `itemOrder` , `itemStatus` , `catId` ) ;";
        $this->db->query($q)->execute();
    }
} 