<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/6/13
 * Time: 3:01 PM
 */
namespace Page\Model;

use Application\API\App;
use Category\Model\CategoryItemTable;
use System\DB\BaseTableGateway;
use Theme\API\Common;
use Zend\Db;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway;

class PageTable extends BaseTableGateway
{
    protected $table = 'tbl_page';
    protected $model = 'Page\Model\Page';
    protected $caches = array('pages_list', 'page_count');
    protected $cache_prefix = array('all_category_article_');
    protected $translateEntityType = array('page', 'content');

    private $pages = null;

    public function getArray($type = 'page')
    {
        $cache_key = 'pages_list_' . $type;
        if (!$list = getCacheItem($cache_key)) {
            $where = array('status' => 1);
            if ($type == 'page')
                $where['isStaticPage'] = 1;
            else
                $where['isStaticPage'] = 0;

            $data = $this->getAll($where, array('pageTitle ASC'));
            $list = array();
            foreach ($data as $page) {
                $list[$page->id] = $page->pageTitle;
            }
            setCacheItem($cache_key, $list);
        }
        return $list;
    }

    public function search($term, $isStatic = 0)
    {
        $where = array('status' => 1);
        $where['isStaticPage'] = $isStatic;
        $where['pageTitle like ?'] = '%' . $term . '%';

        return $this->getAll($where, array('pageTitle ASC'));
    }

    public function getUrl($pageIdOrPage, $mvc = false)
    {
        $cache_key = 'page_urls';
        if (!is_object($pageIdOrPage)) {
            $pageId = $pageIdOrPage;

            if (is_null($this->pages)) {
                if (!$this->pages = getCacheItem($cache_key))
                    $this->pages = array();
            } else
                if (isset($this->pages[$pageId]))
                    return $this->pages[$pageId];
        } else
            $pageId = $pageIdOrPage->id;


        if ($pageIdOrPage instanceof \Page\Model\Page)
            $page = $pageIdOrPage;
        else
            $page = $this->get($pageId);

        if ($page) {
            if ($page->isStaticPage) {
                $url = url('app/page-view', array('id' => $pageId, 'title' => App::prepareUrlString($page->pageTitle)));
            } else
                $url = url('app/single-content', array('id' => $pageId, 'title' => App::prepareUrlString($page->pageTitle)));
        } else
            $url = '#';

        $this->pages[$pageId] = $url;
        if (!$pageIdOrPage instanceof \Page\Model\Page)
            setCacheItem($cache_key, $this->pages);

        return $url;
    }

    public function getUrlCategoryArticle($catId, $catName)
    {
        return url('app/content', array('tagId' => $catId, 'tagName' => $catName));
    }

    public function getPages($tagId, $pageNumber = 0, $frontPage = false, $getArray = false, $limit = false)
    {
        $where = array(
            $this->table . '.status' => 1,
            $this->table . '.isStaticPage' => 0
        );

        if ($frontPage)
            $where[$this->table . '.published'] = 1;
        $sql = $this->getSql();
        $select = $sql->select();
        getSM('translation_api')->translate($select, array('page', 'content'));
        getSM('translation_api')->filter($select, 'PAGE', $this->table . '.id');

        if ($tagId) {
            $select->join(array('t' => 'tbl_tags_page'), $this->table . '.id=t.pageId', array());
            $where['t.tagsId'] = $tagId;
        }

        if (getSM()->has('domain_api')) {
            $domainApi = getSM('domain_api');
            $domainCount = getSM('domain_table')->getCount();
            if ($domainCount > 1)
                $domainApi->filter($select, 'PAGE', $this->table . '.id');
        }

        $select->where($where);
        $select->order(array($this->table.'.id DESC',$this->table.'.order DESC'));
        if ($limit)
            $select->limit((int)$limit);

        if ($pageNumber) {
            $result = $this->getPaginated($select, $sql, $pageNumber,$limit);
        } else {
            $result = $this->selectWith($select);
        }
        if ($getArray) {
            $dataArray = array();
            foreach ($result as $row)
                $dataArray[] = (array)$row;
            return $dataArray;
        } else
            return $result;
    }

    public function getContent($tagId, $count, $customType, $LastId = 0)
    {
        $this->swapResultSetPrototype();
        $where = array(
            $this->table . '.id > ?' => $LastId,
            $this->table . '.status' => 1,
            't.tagsId' => $tagId,
        );
        $order = 'publishUp DESC';
        $sql = $this->getSql();
        $select = $sql->select();
        $select->where($where);
        if ($LastId == 0)
            $select->columns(array('id', 'pageTitle', 'fullText', 'introText', 'publishUp', 'image'));
        $select->join(array('t' => 'tbl_tags_page'), $this->table . '.id=t.pageId');
        getSM('translation_api')->translate($select, array('page', 'content'));
        getSM('translation_api')->filter($select, 'PAGE', $this->table . '.id');

        if (getSM()->has('domain_api')) {
            $domainApi = getSM('domain_api');
            $domainCount = getSM('domain_table')->getCount();
            if ($domainCount > 1)
                $domainApi->filter($select, 'PAGE', $this->table . '.id');
        }

        if ($customType == 1)
            $order = new Db\Sql\Expression('RAND()');
        $select->order(array($order));
        $select->limit((int)$count);

        $dataOneLevel = $this->selectWith($select);
        $this->swapResultSetPrototype();
        return $dataOneLevel;
    }

    public function systemSearch($keyword)
    {
        $keyword = '%' . $keyword . '%';

        $select = $this->getSql()->select();

        //table to check the data for match (original or translated)
        $table = $this->table;

        $columns = array('id', 'isStaticPage');
        $tTable = getSM('translation_api')->translate($select, array('page', 'content'));
        getSM('translation_api')->filter($select, 'PAGE', $this->table . '.id');

        //if translation is not being done
        if ($tTable === false) {
            $columns = array_merge($columns, array('pageTitle', 'fullText', 'introText',));
        } else {
            //check the translated table for data match
            $table = $tTable;
        }

        $where1 = new Db\Sql\Where();
        $where1->equalTo('isStaticPage', 1);

        $where1_sub = new Db\Sql\Where();
        $where1_sub->like($table . '.pageTitle', $keyword)
            ->or->like($table . '.fullText', $keyword);

        $where1->addPredicate($where1_sub);

        $where2 = new Db\Sql\Where();
        $where2->equalTo('isStaticPage', 0)->equalTo('status', 1);

        $where2_sub = new Db\Sql\Where();
        $where2_sub
            ->like($table . '.pageTitle', $keyword)
            ->or->like($table . '.fullText', $keyword)
            ->or->like($table . '.introText', $keyword);

        $where2->addPredicate($where2_sub);

        $where = new Db\Sql\Where();
        $where->addPredicate($where1)
            ->addPredicate($where2, 'OR');

        $select
            ->columns($columns)
            ->where($where)
            ->order(array('pageTitle ASC'))
            ->limit(10);

        return $this->selectWith($select);
    }

    public function getCounts()
    {
        $dataArray = array();
        $sql = $this->getSql();
        $select = $sql->select();
        $select->columns(array(new Db\Sql\Expression('COUNT(tbl_page.isStaticPage) AS id'), 'isStaticPage'));
        $select->group($this->table . '.isStaticPage');
        $data = $this->selectWith($select);
        if ($data->count())
            foreach ($data as $row)
                $dataArray[$row->isStaticPage] = $row->id;
        return $dataArray;
    }

    public function  getExpired()
    {
        return $this->getAll(array('status' => 1, 'publishDown < ?' => time()));
    }

    public function save($model)
    {
        $id = parent::save($model);
        if (getSM()->has('domain_content_table')) {
            $domainContentTable = getSM('domain_content_table');
            $domainContentTable->removeByEntity($model->id, 'PAGE');
            if (isset($model->domains)) {
                if (is_array($model->domains) && count($model->domains)) {
                    $domainContentTable->add($model->domains, $model->id, 'PAGE');
                }
            }
        }
        if (getSM()->has('language_content_table')) {
            $languageContentTable = getSM('language_content_table');
            $languageContentTable->removeByEntity($model->id, 'PAGE');
            if (isset($model->languages)) {
                if (is_array($model->languages) && count($model->languages)) {
                    $languageContentTable->add($model->languages, $model->id, 'PAGE');
                }
            }
        }
        return $id;
    }

    public function get($id)
    {
        $select = $this->getSql()->select();
//        getSM('translation_api')->translate($select, array('page', 'content'), $this->table . '.id');
        $select->where(array('id' => $id));
        $page = $this->selectWith($select);
        if ($page) {
            if ($page->count() == 1) {
                $page = $page->current();
                if (getSM()->has('domain_content_table')) {
                    $page->domains = getSM('domain_content_table')->getDomains($page->id, 'PAGE');
                }
                if (getSM()->has('language_content_table')) {
                    $page->languages = getSM('language_content_table')->getLangs($page->id, 'PAGE');
                }
            } elseif (!$page->count())
                $page = null;
        }
        return $page;
    }

    public function remove($id)
    {
        $imageUrl = array();
        getSM('page_tags_table')->removeByPageId($id);
        $select = $this->getAll(array('id' => $id));
        if ($select->count()) {
            foreach ($select as $row)
                if ($row->image) {
                    $image = unserialize($row->image);
                    if (isset($image['image']) && $image['image'])
                        $imageUrl[] = $image['image'];
                }
        }
        if (getSM()->has('domain_content_table')) {
            getSM('domain_content_table')->removeByEntity($id, 'PAGE');
        }
        if (getSM()->has('language_content_table')) {
            getSM('language_content_table')->removeByEntity($id, 'PAGE');
        }
        parent::remove($id);
        return $imageUrl;
    }

    public function getPageList(Db\Sql\Select $select, $isStaticPage)
    {
        if (!$isStaticPage) {
            $select->join(array('t' => 'tbl_tags_page'), $this->table . '.id=t.pageId', array());
            $select->join(array('ci' => 'tbl_category_item'), 't.tagsId=ci.id', array('itemName' => new Db\Sql\Expression('GROUP_CONCAT(itemName)')));
            $select->group($this->table . '.id');
        }
        $select->where(array('isStaticPage' => $isStaticPage));
        /*  print $select->getSqlString($this->getAdapter()->getPlatform());
          die;*/
        if (getSM()->has('domain_api')) {
            $domainApi = getSM('domain_api');
            $domainCount = getSM('domain_table')->getCount();
            if ($domainCount > 1)
                $domainApi->filter($select, 'PAGE', $this->table . '.id');
        }
    }

    public function getById($id)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        getSM('translation_api')->translate($select, array('page', 'content'));
        getSM('translation_api')->filter($select, 'PAGE', $this->table . '.id');
        $select->where(array('id' => $id));
        if (getSM()->has('domain_api')) {
            $domainApi = getSM('domain_api');
            $domainCount = getSM('domain_table')->getCount();
            if ($domainCount > 1)
                $domainApi->filter($select, 'PAGE', $this->table . '.id');
        }
        return $this->selectWith($select)->current();
    }

    public function createLinkItems($parents, $pId)
    {
        $items = array();
        $this->__sortItemForSiteMap($parents, $pId, $items);
        return $items;
    }

    private function __sortItemForSiteMap(&$parents, $pId, &$items)
    {
        if (isset($parents[$pId])) {
            foreach ($parents[$pId] as $item) {
                $items[$item->id]['data'] = Common::Link($item->itemName, url('app/content', array('tagId' => $item->id, 'tagName' => $item->itemName)));
                $this->__sortItemForSiteMap($parents, $item->id, $items[$item->id]['children']);
            }
        }
    }
}