<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/6/14
 * Time: 12:19 PM
 */

namespace Note\Model;


use System\DB\BaseTableGateway;

class NoteVisibilityTable extends BaseTableGateway
{
    protected $table = 'tbl_note_visibility';
    protected $caches = null;
    protected $cache_prefix = null;
    protected $primaryKey = 'noteId';

    public function getVisibilities($noteId)
    {
        $result = $this->select(array('noteId' => $noteId));
        if ($result) {
            $vs = array();
            foreach ($result as $row) {
                $vs[] = $row['visibility'];
            }
            return $vs;
        }
        return array();
    }
}