<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/25/13
 * Time: 12:40 PM
 */

namespace Theme;


use System\DB\BaseInstall;

class Install extends BaseInstall
{
    public function initialize()
    {
        $select = @$this->params['themes'];
        if (count($select)) {
            $placeholder = array_fill(0, 5, '?');
            $placeholder = '(' . implode(',', $placeholder) . ')';

            $placeholder = implode(',', array_fill(0, count($select), $placeholder));
            $q = "INSERT INTO `tbl_themes` (`id`, `name`,`type`,`default`,`locked`) VALUES " . $placeholder . ";";
            $data = array();
            foreach ($select as $row) {
                $data[] = $row->id;
                $data[] = $row->name;
                $data[] = $row->type;
                $data[] = $row->default;
                $data[] = $row->locked;
            }
            $this->db->query($q)->execute($data);
        }
    }

    public function update_151(){
        $q = "ALTER TABLE `tbl_themes` ADD INDEX ( `default` ) ;";
        $q .= "ALTER TABLE `tbl_themes` ADD INDEX ( `type` ) ;";
        $q .= "ALTER TABLE `tbl_themes` ADD INDEX ( `name` ) ;";
        $this->db->query($q)->execute();
    }

}