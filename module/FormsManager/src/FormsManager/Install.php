<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/14/14
 * Time: 2:03 PM
 */

namespace FormsManager;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function update_127()
    {
        $q = "ALTER TABLE `tbl_forms` MODIFY COLUMN `format` mediumtext";
        $this->db->query($q)->execute();
    }

    public function update_128()
    {
        $q = "ALTER TABLE `tbl_forms` ADD COLUMN `config` text";
        $this->db->query($q)->execute();
    }

    public function update_135(){
        $q = "ALTER TABLE `tbl_forms_data` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        $this->db->query($q)->execute();
    }

    public function update_136(){
        $q = "ALTER TABLE `tbl_forms_data` ADD INDEX ( `formId` );";
        $q .= "ALTER TABLE `tbl_forms_data` ADD INDEX ( `userId` );";
        $q .= "ALTER TABLE `tbl_forms_data` ADD INDEX ( `formId` );";
        $q .= "ALTER TABLE `tbl_forms_data` ADD INDEX ( `formId` , `userId` );";
        $this->db->query($q)->execute();
    }
} 