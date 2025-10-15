<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ajami
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace Note\View\Helper;

use Note\Module;
use System\View\Helper\BaseHelper;


class Notes extends BaseHelper
{
    public function __invoke($entityType, $entityId, $allowAdd = true)
    {
        $vars = array(
            'entityId' => $entityId,
            'entityType' => $entityType,
        );
        if ($allowAdd || isAllowed(Module::NOTE_NEW_ALL)) {
            $data = getSM('user_table')->getUser(current_user()->id, array(
                'table' => array('username', 'displayName'),
                'profile' => array('firstName', 'lastName', 'image')
            ));

            $data['owner'] = current_user()->id;
            $data['note'] = '__NOTE__';
            $data['date'] = time();
            $data['id'] = '__ID__';

            $allowAdd = isAllowed(\Note\Module::NOTE_NEW);

            $vars['template'] = $this->view->render('note/note', array('row' => $data, 'now' => time()));
            $vars['allowedAdd'] = $allowAdd;
        }

        $select = getSM('note_table')->getNotes($entityType, $entityId);

        $vars['data'] = $select;
        return $this->view->render('note/notes', $vars);
    }
}