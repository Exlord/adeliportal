<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Koushan
 * Date: 7/23/13
 * Time: 10:41 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Comment\View\Helper;


class Comment extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke($entityId, $entityType, $commentStatus = 2) //comment status = 2 : inherited
    {
        if (isAllowed(\Comment\Module::APP_COMMENT)) {
            $typeComment = 0;
            $closedShow = -10;
            $questStatus = 0; //ejaze nazar dadan nadarad
            $countComment = getSM('comment_table')->getAll(array('entityId' => $entityId, 'entityType' => $entityType, 'parentId' => 0, 'status' => 1))->count();
            $config = getSM('config_table')->getByVarName('comment')->varValue;
            if (isset($config['questStatus']) && $config['questStatus'])
                $questStatus = (int)$config['questStatus'];
            if (isset($config['count']) && $config['count'])
                $countShowComment = $config['count'];
            else
                $countShowComment = 8;
            if (isset($config['type']) && $config['type'])
                $typeComment = $config['type'];
            if (isset($config['closedShow']) && $config['closedShow'])
                $closedShow = $config['closedShow'];
            $html = $this->view->render('comment/comment/comment', array('ei' => $entityId, 'et' => $entityType, 'count' => $countComment, 'countShowComment' => $countShowComment, 'typeComment' => $typeComment, 'commentStatus' => $commentStatus, 'closedShow' => $closedShow , 'questStatus'=>$questStatus));
            return $html;
        }
    }

}