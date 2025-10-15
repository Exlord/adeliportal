<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ajami
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Note\Model;

use Note\Module;
use System\DB\BaseTableGateway;
use Zend\Db;
use Zend\Db\TableGateway;

class NoteTable extends BaseTableGateway
{
    protected $table = 'tbl_note';
    protected $model = 'Note\Model\Note';
    protected $caches = null;
    protected $cache_prefix = null;

    public function getItem($id)
    {
        return parent::get($id);
    }

    public function get($id)
    {
        $item = parent::get($id);
        $item->visibility = getSM('note_visibility_table')->getVisibilities($id);
        return $item;
    }

    public function getNotes($entityType, $entityId)
    {
        $this->swapResultSetPrototype();
        $select = $this->getSql()->select();
        $select
            ->columns(array('id', 'date', 'owner', 'note'))
            ->join(array('u' => 'tbl_users'), $this->table . '.owner=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile', 'image'), 'LEFT')
            ->join(array('v' => 'tbl_note_visibility'), $this->table . '.id=v.noteId', array(), 'LEFT')
            ->where(array($this->table . '.entityId' => $entityId, $this->table . '.entityType' => $entityType))
            ->order(array($this->table . '.date DESC'));

        $where = new Db\Sql\Where();
        //my own notes
        $where->equalTo($this->table . '.owner', current_user()->id);
        //or it has a public visibility
        $where->or->equalTo('v.visibility', 'public');
        //or i have permission to see all notes
        if (isAllowed(Module::NOTE_VIEW_ALL))
            $where->or->equalTo('v.visibility', 'auth');

        //allow other modules to set filter conditions for their own visibility levels
        getSM('note_api')->setVisibilityFilter($entityType, $entityId, $where, $select);

        $result = $this->selectWith($select);
        $this->swapResultSetPrototype();
        return $result;
    }

    public function save($model)
    {
        $visibilities = $model->visibility;
        $result = parent::save($model);
        getSM('note_visibility_table')->delete($model->id);

        $visibility = array();
        foreach ($visibilities as $vis)
            $visibility[] = array(
                'noteId' => $model->id,
                'visibility' => $vis
            );

        getSM('note_visibility_table')->multiSave($visibility);
        return $result;
    }
}
